<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_local_atividade = trim($_GET["id"]);
            $sql = "
            SELECT l.*, 
            t.tipo_ambiente,
            c.grau_risco AS grau_risco_empresa,e.razao_social AS empresa
            FROM locais_atividade l
            LEFT JOIN tipos_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
            LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
            LEFT JOIN cnae c ON (c.id_cnae = e.id_cnae)
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
            c.grau_risco AS grau_risco_empresa,
            COALESCE(e2.razao_social, e1.razao_social) AS razao_social_local,
            COALESCE(e2.nr_doc, e1.nr_doc) AS nr_doc_local,
            COALESCE(e2.id_tipo_orgao, e1.id_tipo_orgao) AS id_tipo_orgao_local,
            e1.razao_social AS razao_social_empresa,
            e1.nr_doc AS nr_doc_empresa,
            e1.id_tipo_orgao AS id_tipo_orgao_empresa
            FROM locais_atividade l
            LEFT JOIN tipos_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
            LEFT JOIN empresas e1 ON e1.id_empresa = l.id_empresa
            LEFT JOIN empresas e2 ON e2.id_empresa = l.id_empresa_local_atividade
            LEFT JOIN cnae c ON (c.id_cnae = e1.id_cnae OR c.id_cnae = e2.id_cnae)
            WHERE l.ativo = '1'
            AND l.id_empresa = :id_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT l.id_local_atividade,l.id_empresa,l.id_tipo_ambiente,l.id_empresa_local_atividade,l.atividade_principal,l.ativo,
            COALESCE(e2.razao_social, e1.razao_social) AS razao_social_local,
            COALESCE(e2.nr_doc, e1.nr_doc) AS nr_doc_local,
            COALESCE(e2.id_tipo_orgao, e1.id_tipo_orgao) AS id_tipo_orgao_local,
            e1.razao_social AS razao_social_empresa,
            e1.nr_doc AS nr_doc_empresa,
            e1.id_tipo_orgao AS id_tipo_orgao_empresa
            FROM locais_atividade l
            LEFT JOIN empresas e1 ON e1.id_empresa = l.id_empresa
            LEFT JOIN empresas e2 ON e2.id_empresa = l.id_empresa_local_atividade
            WHERE l.ativo = '1'
            ORDER BY e1.razao_social,l.razao_social
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
