<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_permissao = trim($_GET["id"]);
            $sql = "SELECT *
                    FROM permissoes
                    WHERE id_permissao = :id_permissao
                    AND ativo = '1'
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_permissao', $id_permissao);
        } else if (isset($_GET["nome"])) {
            $nome = trim($_GET["nome"]);
            $sql = "SELECT *
                    FROM permissoes 
                    WHERE nome = :nome
                    AND ativo = '1'
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
        } else {
            $sql = "SELECT * FROM permissoes";
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
