<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'] ?? 0;

    $id_pcmso = $postjson['id_pcmso'] ?? 0;
    $id_pgr = $postjson['id_pgr'] ?? 0;
    $id_ltcat = $postjson['id_ltcat'] ?? 0;

    // VERIFICA QUAL ID PARA BUSCA
    if ($id_pcmso > 0) {
        $where = "
        AND r.id_revisao = $id
        ";
    }

    // VERIFICA QUAL ID DO DOCUMENTO PARA BUSCA
    if ($id_pcmso > 0) {
        $where .= "
        AND r.id_pcmso = $id_pcmso
        ";
    } elseif ($id_pgr > 0) {
        $where .= "
        AND r.id_pgr = $id_pgr
        ";
    } elseif ($id_ltcat > 0) {
        $where .= "
        AND r.id_ltcat = $id_ltcat
        ";
    }

    $sql = "
    SELECT * , DATE_FORMAT(r.data, '%d/%m/%Y') data_format
    FROM revisoes AS r
    WHERE r.ativo = 1
    $where
    ORDER BY r.data
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            if ($row->status == 1) {
                $row->status_format = '
                <div class="alert mb-0 alert-primary text-center" role="alert">ABERTA</div>
                ';
            } else {
                $row->status_format = '
                <div class="alert mb-0 alert-danger text-center" role="alert">FECHADA</div>
                ';
            }


            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhuma Revisão foi encontrado'
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
            'success' => false,
            'result' => 'Falha ao carregar registros.'
        ));
    }

    echo $result;
    exit;
}
