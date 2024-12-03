<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_classificacao_empresa = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM classificacao_empresa
            WHERE ativo = '1'
            AND id_classificacao_empresa = :id_classificacao_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_classificacao_empresa', $id_classificacao_empresa);
        } else {
            $sql = "
            SELECT *
            FROM classificacao_empresa
            WHERE ativo = '1'
            ORDER BY classificacao_empresa
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
