<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $status = trim($postjson['status']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND p.id_proposta = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    $sql = "
        SELECT p.*,p.nr_proposta nr_documento,p.corpo_documento,
        la.razao_social local_atividade,
        e.razao_social,
        sp.status_proposta
        FROM propostas p
        JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
        JOIN empresas e ON (la.id_empresa = e.id_empresa)
        JOIN status_propostas sp ON (sp.id_status_proposta = p.id_status_proposta)
        WHERE p.ativo = '1'
        $where
        ORDER BY FIELD(p.id_status_proposta,2,1,3,4,5),e.razao_social,la.razao_social
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            $row->titulo_documento = 'Proposta de Trabalho';

            // FORMATAR STATUS
            if ($row->status_proposta == 'RECUSADO') {
                $row->status_mask = '
                    <div class="alert mb-0 alert-danger text-center" role="alert">
                    Recusado
                    </div>
                ';
            } elseif ($row->status_proposta == 'CANCELADO') {
                $row->status_mask = '
                    <div class="alert mb-0 alert-secondary text-center" role="alert">
                    Cancelado
                    </div>
                ';
            } elseif ($row->status_proposta == 'ENVIADO') {
                $row->status_mask = '
                    <div class="alert mb-0 alert-warning text-center" role="alert">
                    Enviado
                    </div>
                ';
            } elseif ($row->status_proposta == 'CONFECÇÃO') {
                $row->status_mask = '
                    <div class="alert mb-0 alert-primary text-center" role="alert">
                    Confecção
                    </div>
                ';
            } elseif ($row->status_proposta == 'ACEITO') {
                $row->status_mask = '
                    <div class="alert mb-0 alert-success text-center" role="alert">
                    Aceito
                    </div>
                ';
            }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum documento de Proposta foi encontrado'
        ));
        echo $result;
        exit;
    }
}

if ($query) {
    $result = json_encode(array(
        'success' => true,
        'result' => $dados,
    ));
} else {
    $result = json_encode(array(
        'success' => false
    ));
}

echo $result;
exit;
