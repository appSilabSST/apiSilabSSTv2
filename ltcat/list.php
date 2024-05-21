<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
        AND l.id_ltcat = $id
        ";
    }

    $sql = "
    SELECT l.id_ltcat, l.nr_ltcat, l.nr_ltcat nr_documento, l.data_inicio, DATE_FORMAT(l.data_inicio, '%d/%m/%Y') data_inicio_format, l.responsavel, l.responsavel_cpf, l.responsavel_email,l.grau_risco_empresa,l.grau_risco_local_atividade,l.id_profissional,l.consideracoes_finais, l.corpo_documento,
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
    $where
    ORDER BY l.nr_ltcat
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $id_ltcat_vencido = array();
        while ($row = mysqli_fetch_object($query)) {

            $row->disable_status = false;

            $row->titulo_documento = 'LTCAT - Laudo Técnico das Condições de Trabalho';

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

        if (count($id_ltcat_vencido) > 0) {
            $update = "
            UPDATE ltcat SET
            id_status_documento = 5
            WHERE id_ltcat IN (" . implode(',', $id_ltcat_vencido) . ")
            ";

            // echo $update;exit;
            mysqli_query($conecta, $update);
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum documento LTCAT foi encontrado.'
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
