<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_agendamento_exame = trim($_GET["id"]);

            $sql = "
            SELECT rl.id_agendamento, rl.id_exame, rl.id_rl_agendamento_exame, rl.data, DATE_FORMAT(rl.data, '%d/%m/%Y') data_format, rl.pago, rl.id_resultado_exame, rl.id_reaproveitado,
            e.procedimento, e.cod, IF(e.cod IS NOT NULL, CONCAT(e.procedimento, ' | eSocial: ' , e.cod), e.procedimento) procedimento_format,
            (
                SELECT IF(COUNT(id_rl_agendamento_exame) > 0, TRUE, FALSE)
                FROM rl_agendamento_exames
                WHERE id_exame = rl.id_exame
                AND id_agendamento <> rl.id_agendamento
                AND realizado = 1
                AND id_agendamento IN (
                    SELECT id_agendamento
                    FROM agendamentos
                    WHERE id_rl_colaborador_empresa = (
                        SELECT id_rl_colaborador_empresa
                        FROM agendamentos
                        WHERE id_agendamento = rl.id_agendamento
                    )
                )
                AND rl.data <= (
                    SELECT DATE_ADD(rl_agendamento_exames.data, INTERVAL validade MONTH)
                    FROM exames
                    WHERE id_exame = rl.id_exame
                )
            ) reaproveitado
            FROM rl_agendamento_exames rl
            LEFT JOIN exames e ON e.id_exame = rl.id_exame
            WHERE rl.ativo = '1' 
            AND rl.id_rl_agendamento_exame = :id_rl_agendamento_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_exame', $id_rl_agendamento_exame);
        }
        // SELECIONAR EXAMES DE UM AGENDAMENTO ESPECÍFICO
        elseif (isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])) {
            $id_agendamento = trim($_GET["id_agendamento"]);

            $sql = "
            SELECT rl.id_agendamento, rl.id_exame, rl.id_rl_agendamento_exame, rl.data, DATE_FORMAT(rl.data, '%d/%m/%Y') data_format, rl.pago, rl.id_resultado_exame, rl.id_reaproveitado,
            e.procedimento, e.cod, IF(e.cod IS NOT NULL, CONCAT(e.procedimento, ' | eSocial: ' , e.cod), e.procedimento) procedimento_format,
            (
                SELECT IF(COUNT(id_rl_agendamento_exame) > 0, TRUE, FALSE)
                FROM rl_agendamento_exames
                WHERE id_exame = rl.id_exame
                AND id_agendamento <> rl.id_agendamento
                AND realizado = 1
                AND id_agendamento IN (
                    SELECT id_agendamento
                    FROM agendamentos
                    WHERE id_rl_colaborador_empresa = (
                        SELECT id_rl_colaborador_empresa
                        FROM agendamentos
                        WHERE id_agendamento = rl.id_agendamento
                    )
                )
                AND rl.data <= (
                    SELECT DATE_ADD(rl_agendamento_exames.data, INTERVAL validade MONTH)
                    FROM exames
                    WHERE id_exame = rl.id_exame
                )
            ) reaproveitado
            FROM rl_agendamento_exames rl
            LEFT JOIN exames e ON e.id_exame = rl.id_exame
            WHERE rl.ativo = '1'
            AND rl.id_agendamento = :id_agendamento
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
        }
        // RETORNA MENSAGEM INFORMAÇÃO A OBRIGATORIEDADE EM ENVIAR UMA DATA
        else {
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
            $conn = null;
            echo json_encode($result);
            exit;
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum exame foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
    } catch (\Throwable $th) {
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
