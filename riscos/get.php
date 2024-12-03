<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_risco = trim($_GET["id"]);
            $sql = "
            SELECT id_risco, cod_esocial, descricao, grupo, cor, danos_saude
            FROM riscos
            WHERE ativo = '1'
            AND id_risco = :id_risco
            ORDER BY descricao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_risco', $id_risco);
        } else {
            $sql = "
            SELECT id_risco, cod_esocial, descricao, grupo, cor, danos_saude
            FROM riscos
            WHERE ativo = '1'
            ORDER BY descricao
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
    } catch (\Throwable $th) {
        http_response_code(502);
        $result = $th->getMessage();
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
