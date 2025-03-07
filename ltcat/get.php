<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_ltcat = trim($_GET["id"]);
            $sql = "
                SELECT l.*, DATE_FORMAT(l.data_inicio, '%d/%m/%Y') data_inicio_format,
                e.nome_fantasia,e.nr_doc,e.id_tipo_orgao,e.razao_social,
                la.razao_social AS nome_local,la.nr_inscricao,la.id_tipo_orgao as id_tipo_orgao_local,
                e2.id_empresa as id_empresa_local,
                ta.tipo_ambiente,ta.id_tipo_ambiente,
                COUNT(r.id_revisao) as isRevisoes,
                pro.nome,pro.cpf,to_p.siglas as orgao_profissional,pro.orgao_nr,pro.orgao_uf
                FROM ltcat l
                LEFT JOIN locais_atividade la ON (l.id_local_atividade = la.id_local_atividade)
                LEFT JOIN empresas e ON (e.id_empresa = l.id_empresa)
                LEFT JOIN empresas e2 ON (e2.nr_doc = la.nr_inscricao)
                LEFT JOIN revisoes r ON (r.id_ltcat  = l.id_ltcat)
                LEFT JOIN tipos_ambiente ta ON (ta.id_tipo_ambiente = la.id_tipo_ambiente)
                LEFT JOIN profissionais pro ON (l.id_profissional = pro.id_profissional)       
                LEFT JOIN tipos_orgao to_p ON (to_p.id_tipo_orgao = pro.id_tipo_orgao)   
                WHERE l.ativo = 1
                AND l.id_ltcat = :id_ltcat
                GROUP BY l.id_ltcat
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ltcat', $id_ltcat);
        } elseif (isset($_GET["id_empresa"]) && is_numeric($_GET["id_empresa"])) {
            $id_empresa = trim($_GET["id_empresa"]);
            $sql = "
            SELECT l.id_ltcat, l.nr_ltcat, l.nr_ltcat nr_documento, l.data_inicio, DATE_FORMAT(l.data_inicio, '%d/%m/%Y') data_inicio_format, l.responsavel, l.responsavel_cpf, l.responsavel_email,l.grau_risco_empresa,l.grau_risco_local_atividade,l.id_profissional,l.consideracoes_finais,
            e.id_empresa, e.razao_social,
            la.id_local_atividade, la.razao_social nome_local_atividade,
            s.id_status_documento, s.status_documento,
            pro.nome nome_profissional
            FROM ltcat l
            LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
            LEFT JOIN locais_atividade la ON (l.id_local_atividade = la.id_local_atividade)
            LEFT JOIN status_documentos s ON (s.id_status_documento = l.id_status_documento)
            LEFT JOIN profissionais pro ON (l.id_profissional = pro.id_profissional)
            WHERE l.ativo = 1
            AND l.id_empresa = :id_empresa
            ORDER BY l.nr_ltcat
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', $id_empresa);
        } else {
            $sql = "
            SELECT l.*, DATE_FORMAT(l.data_inicio, '%d/%m/%Y') data_inicio_format,
            e.nome_fantasia,e.nr_doc,e.id_tipo_orgao,e.razao_social,
 		   	la.razao_social AS nome_local,la.nr_inscricao,la.id_tipo_orgao as id_tipo_orgao_local,
            e2.id_empresa as id_empresa_local,
            ta.tipo_ambiente,ta.id_tipo_ambiente,
            COUNT(r.id_revisao) as isRevisoes,
            pro.nome,pro.cpf,to_p.siglas as orgao_profissional,pro.orgao_nr,pro.orgao_uf
            FROM ltcat l
            LEFT JOIN locais_atividade la ON (l.id_local_atividade = la.id_local_atividade)
            LEFT JOIN empresas e ON (e.id_empresa = l.id_empresa)
            LEFT JOIN empresas e2 ON (e2.nr_doc = la.nr_inscricao)
            LEFT JOIN revisoes r ON (r.id_ltcat  = l.id_ltcat)
            LEFT JOIN tipos_ambiente ta ON (ta.id_tipo_ambiente = la.id_tipo_ambiente)
            LEFT JOIN profissionais pro ON (l.id_profissional = pro.id_profissional)
            LEFT JOIN tipos_orgao to_p ON (to_p.id_tipo_orgao = pro.id_tipo_orgao)  
            WHERE l.ativo = 1
            GROUP BY l.id_ltcat
            ORDER BY l.nr_ltcat
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
