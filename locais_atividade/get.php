<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_local_atividade = trim($_GET["id"]);
            $sql = "
            SELECT l.*, 
            t.tipo_ambiente,
            e.grau_risco AS grau_risco_empresa,e.razao_social AS empresa
            FROM locais_atividade l
            LEFT JOIN tipo_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
            LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
            WHERE l.ativo = '1'
            AND l.id_local_atividade = :id_local_atividade
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_local_atividade', $id_local_atividade);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT l.*, 
            t.tipo_ambiente,
            e.grau_risco AS grau_risco_empresa,e.razao_social AS empresa
            FROM locais_atividade l
            LEFT JOIN tipo_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
            LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
            WHERE l.ativo = '1'
            AND l.id_empresa = :id_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT l.*, 
            t.tipo_ambiente,
            e.razao_social AS empresa
            FROM locais_atividade l
            LEFT JOIN tipos_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
            LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
            WHERE l.ativo = '1'
            ORDER BY e.razao_social , l.razao_social
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
