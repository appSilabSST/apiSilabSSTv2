<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id_anamnese"]) && is_numeric($_GET["id_anamnese"])) {
            $id_anamnese = trim($_GET["id_anamnese"]);
            $sql = "
            DELETE FROM rl_anamneses_exames
            WHERE id_anamnese = :id_anamnese
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese', $id_anamnese);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Excluido com sucesso!'
            );
        } else if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_rl_anamneses_exames = trim($_GET["id"]);
            $sql = "
            DELETE FROM rl_anamneses_exames
            WHERE id_rl_anamneses_exames = :id_rl_anamneses_exames
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_anamneses_exames', $id_rl_anamneses_exames);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Excluido com sucesso!'
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
