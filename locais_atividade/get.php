<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_local_atividade = trim($_GET["id"]);
            $sql = "
            SELECT l.*, 
            t.tipo_ambiente,
            e.razao_social AS empresa
            FROM locais_atividade l
            LEFT JOIN tipos_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
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
            e.nome_fantasia,
            l.razao_social AS nome_local,
            ta.tipo_ambiente
            FROM locais_atividade l
            JOIN empresas e ON e.id_empresa = l.id_empresa
            LEFT JOIN empresas e2 ON e2.nr_doc = l.nr_inscricao
            JOIN tipos_ambiente ta ON ta.id_tipo_ambiente = l.id_tipo_ambiente
            WHERE l.ativo = '1'
            AND l.id_empresa = :id_empresa
            ORDER BY l.id_tipo_ambiente
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT e.*,e2.id_empresa as id_empresa_local,l.*,
            if(l.id_tipo_orgao = 0,e.id_tipo_orgao,l.id_tipo_orgao) as id_tipo_orgao,
            l.razao_social AS nome_local,
            ta.tipo_ambiente
            FROM locais_atividade l
            JOIN empresas e ON e.id_empresa = l.id_empresa
            LEFT JOIN empresas e2 ON e2.nr_doc = l.nr_inscricao
            JOIN tipos_ambiente ta ON ta.id_tipo_ambiente = l.id_tipo_ambiente
            WHERE l.ativo = '1'
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
            'result' => $th->getMessage(),
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
