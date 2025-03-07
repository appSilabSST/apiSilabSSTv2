<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // ATUALIZA TODOS DOCUMENTOS FINALIZADOS QUE JÁ PASSARAM DA VALIDADE
        $sql = "
        UPDATE pgr SET
        id_status_documento = 5
        WHERE DATE_FORMAT(data_fim, '%Y-%m') < DATE_FORMAT(CURDATE(), '%Y-%m')
        AND id_status_documento = 2
        ";
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_pgr = trim($_GET["id"]);
            $sql = "
                SELECT p.*, 
                DATE_FORMAT(p.data_inicio, '%b/%y') AS data_inicio_format, 
                DATE_FORMAT(p.data_fim, '%Y-%m') AS data_fim_format,
                e.nome_fantasia, e.nr_doc, e.id_tipo_orgao, e.razao_social, 
                e.cidade, e.uf, la.razao_social AS nome_local, 
                la.nr_inscricao, la.id_tipo_orgao AS id_tipo_orgao_local,
                e2.id_empresa AS id_empresa_local,
                pro.nome, pro.cpf, to_p.siglas as orgao_profissional, pro.orgao_nr, pro.orgao_uf
                FROM pgr p
                LEFT JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
                LEFT JOIN empresas e ON (e.id_empresa = p.id_empresa)
                LEFT JOIN empresas e2 ON (e2.nr_doc = la.nr_inscricao)
                LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
                LEFT JOIN tipos_orgao to_p ON (to_p.id_tipo_orgao = pro.id_tipo_orgao)  
                WHERE p.ativo = 1
                AND p.id_pgr = :id_pgr

            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pgr', $id_pgr);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT p.id_pgr, p.nr_pgr, p.nr_pgr nr_documento, DATE_FORMAT(p.data_inicio, '%Y-%m') data_inicio, DATE_FORMAT(p.data_inicio, '%b/%y') data_inicio_format, DATE_FORMAT(p.data_fim, '%Y-%m') data_fim, DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, CONCAT(DATE_FORMAT(p.data_inicio, '%b/%y'), ' - ', DATE_FORMAT(p.data_fim, '%b/%y')) vigencia, p.responsavel, p.responsavel_cpf, p.responsavel_email,p.grau_risco_empresa,p.grau_risco_local_atividade,p.id_profissional,p.consideracoes_finais,
            e.id_empresa, e.razao_social,
            l.id_local_atividade, l.razao_social nome_local_atividade,
            s.id_status_documento, s.status_documento,
            pro.nome nome_profissional
            FROM pgr p
            LEFT JOIN empresas e ON (p.id_empresa = e.id_empresa)
            LEFT JOIN locais_atividade l ON (p.id_local_atividade = l.id_local_atividade)
            LEFT JOIN status_documentos s ON (s.id_status_documento = p.id_status_documento)
            LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
            WHERE p.ativo = 1
            AND p.id_empresa = :id_empresa
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT p.*,
            DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, CONCAT(DATE_FORMAT(p.data_inicio, '%b/%y'), ' - ', DATE_FORMAT(p.data_fim, '%b/%y')) vigencia,
            e.nome_fantasia,e.nr_doc,e.id_tipo_orgao,e.razao_social,
 			la.razao_social AS nome_local,la.nr_inscricao,la.id_tipo_orgao as id_tipo_orgao_local,
            e2.id_empresa as id_empresa_local,
            ta.tipo_ambiente,ta.id_tipo_ambiente,
            COUNT(r.id_revisao) as isRevisoes,
            pro.nome,pro.cpf,to_p.siglas as orgao_profissional,pro.orgao_nr,pro.orgao_uf
            FROM pgr p
            LEFT JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
            LEFT JOIN empresas e ON (e.id_empresa = p.id_empresa)
            LEFT JOIN empresas e2 ON (e2.nr_doc = la.nr_inscricao)
            LEFT JOIN revisoes r ON (r.id_pgr  = p.id_pgr)
            LEFT JOIN tipos_ambiente ta ON (ta.id_tipo_ambiente = la.id_tipo_ambiente)
            LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
            LEFT JOIN tipos_orgao to_p ON (to_p.id_tipo_orgao = pro.id_tipo_orgao)  
            WHERE p.ativo = 1
            GROUP BY p.id_pgr
            ORDER BY p.nr_pgr DESC
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
