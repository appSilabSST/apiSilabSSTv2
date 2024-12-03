<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_meio_propagacao = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM meios_propagacao
            WHERE ativo = '1'
            AND id_meio_propagacao = :id_meio_propagacao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_meio_propagacao', $id_meio_propagacao);
        } else {
            $sql = "
            SELECT *
            FROM meios_propagacao
            WHERE ativo = '1'
            ORDER BY id_meio_propagacao
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
