<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id_sala_atendimento"]) && is_numeric($_GET["id_sala_atendimento"])) {
            $id_sala_atendimento = trim($_GET["id_sala_atendimento"]);
            $sql = "
            DELETE FROM rl_salas_exames
            WHERE id_sala_atendimento = :id_sala_atendimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_sala_atendimento', $id_sala_atendimento);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame removido com sucesso!'
            );
        } else  if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_sala_exame = trim($_GET["id"]);
            $sql = "
            DELETE FROM rl_salas_exames
            WHERE id_rl_sala_exame = :id_rl_sala_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_sala_exame', $id_rl_sala_exame);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame removido com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage(),
            'code' => $th->getCode()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
