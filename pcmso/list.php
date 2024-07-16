<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_empresa = $postjson['id_empresa'];
    $id_rl_colaborador_empresa = $postjson['id_rl_colaborador_empresa'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
        AND p.id_pcmso = $id
        ";
    }
    // SE TIVER ID_EMPRESA PARA BUSCA
    if ($id_empresa > 0) {
        $where = "
        AND p.id_empresa = $id_empresa
        ";
    }
    // SE TIVER id_rl_colaborador_empresa PARA BUSCA
    if ($id_rl_colaborador_empresa > 0) {
        $where = "
        AND p.id_empresa = (
            SELECT id_empresa
            FROM rl_colaboradores_empresas
            WHERE id_rl_colaborador_empresa = $id_rl_colaborador_empresa
        )
        ";
    }

    $sql = "
    SELECT p.id_pcmso, p.nr_pcmso, p.nr_pcmso nr_documento, DATE_FORMAT(p.data_inicio, '%Y-%m') data_inicio, DATE_FORMAT(p.data_inicio, '%b/%y') data_inicio_format, DATE_FORMAT(p.data_fim, '%Y-%m') data_fim, DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, p.responsavel, p.responsavel_cpf, p.responsavel_email,p.grau_risco_empresa,p.grau_risco_local_atividade,p.relatorio_analitico,p.id_profissional,p.consideracoes_finais, p.corpo_documento,
    e.id_empresa, e.razao_social,
    l.id_local_atividade, l.razao_social nome_local_atividade,
    s.id_status_documento, s.status_documento,
    pro.nome nome_profissional
	FROM pcmso p
    LEFT JOIN empresas e ON (p.id_empresa = e.id_empresa)
    LEFT JOIN locais_atividade l ON (p.id_local_atividade = l.id_local_atividade)
    LEFT JOIN status_documentos s ON (s.id_status_documento = p.id_status_documento)
    LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
    WHERE p.ativo = 1
    $where
    ORDER BY p.nr_pcmso DESC
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $id_pcmso_vencido = array();
        while ($row = mysqli_fetch_object($query)) {

            // VERIFICA SE DOCUMENTO ESTÁ VENCIDO PARA MUDAR STATUS_DOCUMENTO
            if (strtotime(date('Y-m-d')) > strtotime($row->data_fim) && $row->status_documento != 'VENCIDO') {
                array_push($id_pcmso_vencido, $row->id_pcmso);
                $row->status_documento = 'VENCIDO';
            }

            $row->vigencia = $row->data_inicio_format . ' - ' . $row->data_fim_format;

            $row->disable_status = false;

            $row->titulo_documento = 'PCMSO - Programa de Controle Médico da Saúde Ocupacional';

            if ($row->status_documento == 'CONFECÇÃO') {
                $row->status_documento_mask = '<div class="alert mb-0 alert-primary text-center" role="alert">CONFECÇÃO</div>';
                $row->icone_status = 'edit-2-outline';
            } elseif ($row->status_documento == 'VIGENTE') {
                $row->status_documento_mask = '<div class="alert mb-0 alert-success text-center" role="alert">VIGENTE</div>';
                $row->icone_status = 'done-all-outline';
            } elseif ($row->status_documento == 'REVISÃO') {
                $row->status_documento_mask = '<div class="alert mb-0 alert-warning text-center" role="alert">REVISÃO</div>';
                $row->icone_status = 'alert-triangle-outline';
            } elseif ($row->status_documento == 'VENCIDO') {
                $row->status_documento_mask = '<div class="alert mb-0 alert-danger text-center" role="alert">VENCIDO</div>';
                $row->disable_status = true;
                $row->icone_status = 'lock-outline';
            } elseif ($row->status_documento == 'CANCELADO') {
                $row->status_documento_mask = '<div class="alert mb-0 alert-info text-center" role="alert">CANCELADO</div>';
                $row->icone_status = 'slash-outline';
                $row->disable_status = true;
            }

            $dados[] = $row;
        }

        if (count($id_pcmso_vencido) > 0) {
            $update = "
            UPDATE pcmso SET
            id_status_documento = 5
            WHERE id_pcmso IN (" . implode(',', $id_pcmso_vencido) . ")
            ";

            // echo $update;exit;
            mysqli_query($conecta, $update);
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum documento PCMSO foi encontrado.'
        ));
        echo $result;
        exit;
    }

    if ($query) {
        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    } else {
        $result = json_encode(array(
            'success' => false
        ));
    }

    echo $result;
    exit;
}
