<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_empresa = trim($_GET["id"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE ativo = '1'
            AND id_empresa = :id_empresa
            ORDER BY status, razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } elseif (isset($_GET["status"]) && is_numeric($_GET["status"])) {
            $status = trim($_GET["status"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE ativo = '1'
            AND status = :status
            ORDER BY status, razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':status', $status);
        } else {
            $sql = "
            SELECT *
            FROM empresas
            WHERE ativo = '1'
            ORDER BY status, razao_social
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhuma empresa foi encontrada'
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
