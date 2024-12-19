<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_empresa = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE id_empresa = :id_empresa
            AND empresas.ativo = '1'
            ORDER BY empresas.status, razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } elseif (isset($_GET["status"]) && is_numeric($_GET["status"])) {
            $status = trim($_GET["status"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE empresas.status = :status
            AND empresas.ativo = '1'
            ORDER BY empresas.status, razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':status', $status);
        } elseif (isset($_GET["nr_doc"]) && is_numeric($_GET["nr_doc"])) {
            $nr_doc = trim($_GET["nr_doc"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE nr_doc = :nr_doc
            AND empresas.ativo = '1'
            ORDER BY empresas., razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', $nr_doc);
        } else {
            $sql = "
            SELECT *,empresas.status 
            FROM empresas
            LEFT JOIN cnae ON (empresas.id_cnae = cnae.id_cnae)
            WHERE empresas.ativo = '1'
            ORDER BY empresas.status, empresas.razao_social
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
