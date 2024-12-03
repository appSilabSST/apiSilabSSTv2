<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_classificacao_risco = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM classificacao_riscos
            WHERE ativo = '1'
            AND id_classificacao_risco = :id_classificacao_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_classificacao_risco', $id_classificacao_risco);
        } else {
            $sql = "
            SELECT *
            FROM classificacao_riscos
            WHERE ativo = '1'
            ORDER BY classificacao_risco
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
