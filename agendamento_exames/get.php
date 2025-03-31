<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_agendamento_exame = trim($_GET["id"]);

            $sql = "
            SELECT  rl.*,
            e.procedimento, e.cod_esocial, IF(e.cod_esocial IS NOT NULL, CONCAT(e.procedimento, ' | eSocial: ' , e.cod_esocial), e.procedimento) procedimento_format,
            (
                SELECT JSON_OBJECT('realizado',r1.data, 'validade',IF(r2.validade > 0, DATE_ADD(r1.data, INTERVAL r2.validade MONTH), 'INDETERMINADO'))
                FROM rl_agendamento_exames r1
                JOIN exames r2 ON r1.id_exame = r2.id_exame
                JOIN agendamentos r3 ON r1.id_agendamento = r3.id_agendamento
                WHERE r1.id_agendamento <> rl.id_agendamento
                AND r3.id_rl_colaborador_empresa = a.id_rl_colaborador_empresa
                AND r1.id_exame = rl.id_exame
                AND r1.reaproveitado = 0
                AND r1.realizado = 1
                AND DATE_ADD(r1.data, INTERVAL r2.validade MONTH) <= rl.data
                ORDER BY r1.data DESC
                LIMIT 1
            ) data_reaproveitado
            FROM rl_agendamento_exames rl
            JOIN agendamentos a ON rl.id_agendamento = a.id_agendamento
            LEFT JOIN exames e ON e.id_exame = rl.id_exame
            WHERE rl.ativo = '1'
            AND rl.id_rl_agendamento_exame = :id_rl_agendamento_exame
            ORDER BY e.procedimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_exame', $id_rl_agendamento_exame);
        } elseif (
            isset($_GET["data"]) && isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])
            && isset($_GET["id_sala"]) && is_numeric($_GET["id_sala"])
        ) {
            $id_agendamento = trim($_GET["id_agendamento"]);
            $data = trim($_GET["data"]);
            $id_sala = trim($_GET["id_sala"]);

            $sql = "
            SELECT rl.*,e.*,
            an.titulo,an.id_anexo,
            av.avaliacao,av.resultado,av.id_avaliacao,av.anamnese
			FROM rl_agendamento_exames rl
            JOIN agendamentos a ON rl.id_agendamento = a.id_agendamento
            JOIN rl_salas_exames rl_se ON (rl_se.id_exame = rl.id_exame)
            LEFT JOIN avaliacao av ON (av.id_rl_agendamento_exame = rl.id_rl_agendamento_exame)
            LEFT JOIN anexos an ON (an.id_rl_agendamento_exame = an.id_rl_agendamento_exame)
            LEFT JOIN exames e ON e.id_exame = rl.id_exame
            WHERE rl.ativo = '1'
            AND rl.id_agendamento = :id_agendamento
            AND rl.`data` = :data
            AND rl_se.id_sala_atendimento = :id_sala
            ORDER BY e.procedimento
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':id_sala', $id_sala);
        }
        // SELECIONAR EXAMES DE UM AGENDAMENTO ESPECÍFICO
        elseif (isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])) {
            $id_agendamento = trim($_GET["id_agendamento"]);

            $sql = "
            SELECT rl.*,
            e.procedimento, e.cod_esocial, IF(e.cod_esocial IS NOT NULL, CONCAT(e.procedimento, ' | eSocial: ' , e.cod_esocial), e.procedimento) procedimento_format,e.valor_cobrar,
            (
                SELECT JSON_OBJECT(
                    'valor', r1.valor,
                    'realizado', r1.data, 
                    'validade', IF(r2.validade > 0, DATE_ADD(r1.data, INTERVAL r2.validade MONTH), 'INDETERMINADO')
                )
                FROM rl_agendamento_exames r1
                JOIN exames r2 ON r1.id_exame = r2.id_exame
                JOIN agendamentos r3 ON r1.id_agendamento = r3.id_agendamento
                WHERE r1.id_agendamento <> rl.id_agendamento
                AND r3.id_rl_colaborador_empresa = a.id_rl_colaborador_empresa
                AND r1.id_exame = rl.id_exame
                AND r1.reaproveitado = 0
                AND r1.realizado = 1
                AND DATE_ADD(r1.data, INTERVAL r2.validade MONTH) <= rl.data
                ORDER BY r1.data DESC
                LIMIT 1
            ) data_reaproveitado
            FROM rl_agendamento_exames rl
            JOIN agendamentos a ON rl.id_agendamento = a.id_agendamento
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

        $result = getResult($stmt);
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
