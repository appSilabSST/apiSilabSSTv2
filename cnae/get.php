<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_cnae = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
            AND id_cnae = :id_cnae
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_cnae', $id_cnae);
        } else if (isset($_GET["codigo"]) && is_numeric($_GET["codigo"])) {
            $codigo = trim($_GET["codigo"]);
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
            AND codigo = :codigo
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':codigo', $codigo);
        } else {
            $sql = "
            SELECT *
            FROM cnae
            WHERE ativo = '1'
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
