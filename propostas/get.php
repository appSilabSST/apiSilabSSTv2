<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_proposta = trim($_GET["id"]);
            $sql = "
            SELECT p.*,
            la.razao_social local_atividade,
            e.razao_social,
            sp.status_proposta
            FROM propostas p
            JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
            JOIN empresas e ON (la.id_empresa = e.id_empresa)
            JOIN status_propostas sp ON (sp.id_status_proposta = p.id_status_proposta)
            WHERE p.ativo = '1'
            AND id_proposta = :id_proposta
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', $id_proposta);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT p.*,
            la.razao_social local_atividade,
            e.razao_social,
            sp.status_proposta
            FROM propostas p
            JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
            JOIN empresas e ON (la.id_empresa = e.id_empresa)
            JOIN status_propostas sp ON (sp.id_status_proposta = p.id_status_proposta)
            WHERE p.ativo = '1'
            AND p.id_empresa = :id_empresa
            ORDER BY FIELD(p.id_status_proposta,2,1,3,4,5),e.razao_social,la.razao_social
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT p.*,
            sp.status_proposta,
            COALESCE(e2.razao_social, e1.razao_social) AS razao_social_local,
            COALESCE(e2.nr_doc, e1.nr_doc) AS nr_doc_local,
            COALESCE(e2.id_tipo_orgao, e1.id_tipo_orgao) AS id_tipo_orgao_local,
            e1.razao_social AS razao_social_empresa,
            e1.nr_doc AS nr_doc_empresa,
            e1.id_tipo_orgao AS id_tipo_orgao_empresa
            FROM propostas p
            LEFT JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
            LEFT JOIN empresas e1 ON e1.id_empresa = p.id_empresa
            LEFT JOIN empresas e2 ON e2.id_empresa = la.id_empresa_local_atividade
            JOIN status_propostas sp ON (sp.id_status_proposta = p.id_status_proposta)
            WHERE p.ativo = '1'
            ORDER BY FIELD(p.id_status_proposta,2,1,3,4,5),razao_social_empresa,razao_social_local
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
