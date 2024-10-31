<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_agendamento_risco = trim($_GET["id"]);
            $sql = "
            SELECT r.cod, IF(r.cod IS NOT NULL, CONCAT(r.descricao, ' | eSocial: ' , r.cod), descricao) agente_nocivo, r.descricao, r.grupo, r.cor, r.danos_saude
            FROM rl_agendamento_riscos rl
            JOIN riscos r ON rl.id_risco = r.id_risco
            WHERE rl.ativo = 1
            AND rl.id_rl_agendamento_risco = :id_rl_agendamento_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_risco', $id_rl_agendamento_risco);
        }
        // SELECIONAR AFASTAMENTOS DE UMA EMPRESA ESPECÍFICA
        elseif (isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])) {
            $id_agendamento = trim($_GET["id_agendamento"]);
            $sql = "
            SELECT r.cod, IF(r.cod IS NOT NULL, CONCAT(r.descricao, ' | eSocial: ' , r.cod), descricao) agente_nocivo, r.descricao, r.grupo, r.cor, r.danos_saude
            FROM rl_agendamento_riscos rl
            JOIN riscos r ON rl.id_risco = r.id_risco
            WHERE rl.ativo = 1
            AND rl.id_agendamento = :id_agendamento
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
            echo json_encode($result);
            exit;
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum risco foi encontrado'
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
