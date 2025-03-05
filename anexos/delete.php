<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
     if (isset($_GET["id_anexo"]) && is_numeric($_GET["id_anexo"])) {
            $id_anexo = trim($_GET["id_anexo"]);
            $sql = "
            DELETE FROM anexos
            WHERE id_anexo = :id_anexo
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_anexo', $id_anexo);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Riscos removidos com sucesso!'
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
