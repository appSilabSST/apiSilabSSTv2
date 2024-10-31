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
            SELECT p.id_pgr, p.nr_pgr, p.nr_pgr nr_documento, DATE_FORMAT(p.data_inicio, '%Y-%m') data_inicio, DATE_FORMAT(p.data_inicio, '%b/%y') data_inicio_format, DATE_FORMAT(p.data_fim, '%Y-%m') data_fim, DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, CONCAT(DATE_FORMAT(p.data_inicio, '%b/%y'), ' - ', DATE_FORMAT(p.data_fim, '%b/%y')) vigencia, p.responsavel, p.responsavel_cpf, p.responsavel_email,p.grau_risco_empresa,p.grau_risco_local_atividade,p.id_profissional,p.consideracoes_finais,p.corpo_documento,
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
            ORDER BY p.nr_pgr DESC
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum PGR foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1) {
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
