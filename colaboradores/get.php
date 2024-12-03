<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_colaborador = trim($_GET["id"]);
            $sql = "
            SELECT *,
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), null) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), null)) rg_format
            FROM colaboradores
            WHERE ativo = '1'
            AND id_colaborador = :id_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_colaborador', $id_colaborador);
        } elseif (isset($_GET["nr_doc"])) {
            $nr_doc = trim($_GET["nr_doc"]);
            $sql = "
            SELECT *,
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), nr_doc) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), rg)) rg_format
            FROM colaboradores
            WHERE ativo = '1'
            AND nr_doc = :nr_doc
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_doc', $nr_doc);
        } else {
            $sql = "
            SELECT *,
                IF(LENGTH(nr_doc) = 11, INSERT( INSERT( INSERT( nr_doc, 10, 0, '-' ), 7, 0, '.' ), 4, 0, '.' ), nr_doc) nr_doc_format,
			    IF(LENGTH(rg) = 9, INSERT( INSERT( INSERT( rg, 9, 0, '-' ), 6, 0, '.' ), 3, 0, '.' ), 
			    IF(LENGTH(rg) = 8, INSERT( INSERT( rg, 6, 0, '.' ), 3, 0, '.' ), rg)) rg_format
            FROM colaboradores
            WHERE ativo = '1'
            ORDER BY nome
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
