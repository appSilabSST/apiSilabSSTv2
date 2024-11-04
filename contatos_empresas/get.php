<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_contato_empresa = trim($_GET["id"]);
            $sql = "
            SELECT ce.*
            FROM contatos_empresas ce
            WHERE ce.ativo = 1
            AND id_contato_empresa = :id_contato_empresa
            ORDER BY ce.nome
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_contato_empresa', $id_contato_empresa);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET['id_empresa'])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT ce.*
            FROM contatos_empresas ce
            WHERE ce.ativo = 1
            AND id_empresa = :id_empresa
            ORDER BY ce.nome
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT ce.*
            FROM contatos_empresas ce
            WHERE ce.ativo = 1
            ORDER BY ce.nome
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum contato da empresa foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
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
