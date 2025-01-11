<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_unidade_medida = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM unidades_medida
            WHERE ativo = '1'
            AND id_unidade_medida = :id_unidade_medida
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_unidade_medida', $id_unidade_medida);
        } else {
            $sql = "
            SELECT *
            FROM unidades_medida
            WHERE ativo = '1'
            ORDER BY id_unidade_medida
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
