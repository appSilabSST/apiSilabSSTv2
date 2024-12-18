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
        } elseif (isset($_GET["nr_inscricao"]) && is_numeric($_GET["nr_inscricao"])) {
            $nr_inscricao = trim($_GET["nr_inscricao"]);
            $sql = "
            SELECT *
            FROM empresas
            WHERE ativo = '1'
            AND stanr_inscricaotus = :nr_inscricao
            ORDER BY status, razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':stanr_inscricaous', $nr_inscricao);
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
