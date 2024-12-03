<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_servico = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM servicos AS s
            WHERE s.ativo = 1
            AND s.id_servico = :id_servico
            ORDER BY s.servico
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_servico', $id_servico);
        } else {
            $sql = "
            SELECT *
            FROM servicos AS s
            WHERE s.ativo = 1
            ORDER BY s.servico
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
