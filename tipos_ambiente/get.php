<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_tipo_ambiente = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM tipos_ambiente
            WHERE ativo = 1
            AND id_tipo_ambiente = :id_tipo_ambiente
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_tipo_ambiente', $id_tipo_ambiente);
        } else {
            $sql = "
            SELECT *
            FROM tipos_ambiente
            WHERE ativo = 1
            ORDER BY id_tipo_ambiente
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(500);
        $result = $th->getMessage();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
