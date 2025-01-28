<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_anamnese_pergunta = trim($_GET["id"]);
            $sql = "
            DELETE FROM anamnese_perguntas
            WHERE id_anamnese_pergunta = :id_anamnese_pergunta
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anamnese_pergunta', $id_anamnese_pergunta);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Pergunta excluida com sucesso!'
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
