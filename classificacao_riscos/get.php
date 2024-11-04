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

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhuma classificação de risco foi encontrada'
            );
        } elseif ($stmt->rowCount() == 1) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
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
