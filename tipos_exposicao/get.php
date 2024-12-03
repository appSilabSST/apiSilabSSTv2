<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_tipo_exposicao = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM tipos_exposicao
            WHERE ativo = '1'
            AND id_tipo_exposicao = :id_tipo_exposicao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_tipo_exposicao', $id_tipo_exposicao);
        } else {
            $sql = "
            SELECT *
            FROM tipos_exposicao
            WHERE ativo = '1'
            ORDER BY id_tipo_exposicao
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
