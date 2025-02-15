<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_cnae = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM rl_empresa_cnae
            WHERE id_rl_empresa_cnae = :id_rl_empresa_cnae
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_cnae', $id_cnae);
        } else if (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT *
            FROM rl_empresa_cnae rl_ce
            JOIN cnae c ON (c.id_cnae = rl_ce.id_cnae)
            WHERE rl_ce.id_empresa = :id_empresa
            ORDER BY rl_ce.classe
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else if (isset($_GET["id_cnae"]) && is_numeric($_GET["id_cnae"])) {
            $id_cnae = trim($_GET["id_cnae"]);
            $sql = "
            SELECT *
            FROM rl_empresa_cnae
            WHERE id_cnae = :id_cnae
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_cnae', $id_cnae);
        } else {
            $sql = "
            SELECT *
            FROM rl_empresa_cnae
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
