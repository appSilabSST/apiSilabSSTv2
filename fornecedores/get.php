<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_fornecedor = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM fornecedores
            WHERE ativo = 1
            AND id_fornecedor = :id_fornecedor
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_fornecedor', $id_fornecedor);
        } else if (isset($_GET["nr_doc"]) && is_numeric($_GET["nr_doc"])) {
            $nr_doc = trim($_GET["nr_doc"]);
            $sql = "
            SELECT *
            FROM fornecedores
            WHERE ativo = 1
            AND nr_doc = :nr_doc
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', $nr_doc);
        } else {
            $sql = "
            SELECT *
            FROM fornecedores
            WHERE ativo = 1
            ORDER BY razao_social
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(500);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
