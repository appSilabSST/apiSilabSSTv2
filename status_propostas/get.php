<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_status_proposta = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM status_propostas
            WHERE ativo = 1
            AND id_status_proposta = :id_status_proposta
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_status_proposta', $id_status_proposta);
        } else {
            $sql = "
            SELECT *
            FROM status_propostas
            WHERE ativo = 1
            ORDER BY id_status_proposta
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
