<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_agendamento_exame = trim($_GET["id"]);
            $sql = "
            DELETE FROM rl_agendamento_exames
            WHERE id_rl_agendamento_exame = :id_rl_agendamento_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_exame', $id_rl_agendamento_exame);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame removido com sucesso!'
            );
        } elseif (isset($_GET["id_agendamento"]) && is_numeric($_GET["id_agendamento"])) {
            $id_agendamento = trim($_GET["id_agendamento"]);
            $sql = "
            DELETE FROM rl_agendamento_exames
            WHERE id_agendamento = :id_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Exames removidos com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(200);
        $result = array(
            "status" => "fail",
            "error" => $th->getMessage()
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
