<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $id_rl_colaborador_empresa = trim($postjson['id_rl_colaborador_empresa']);
    $id_empresa = trim($postjson['id_empresa']);

    $where = "";

    // SE TIVER ID PARA BUSCA
    if ($id_rl_colaborador_empresa > 0) {
        $where .= "
        AND rl.id_rl_colaborador_empresa = " . $id_rl_colaborador_empresa . "
        ";
    }

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND a.id_afastamento = " . $id . "
        ";
    }

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_empresa > 0) {
        $where .= "
        AND rl.id_empresa = " . $id_empresa . "
        ";
    }

    $sql = "
    SELECT a.id_afastamento, a.id_rl_colaborador_empresa, a.data_entrega, DATE_FORMAT(a.data_entrega, '%d/%m/%Y') data_entrega_format, a.data_afastamento, DATE_FORMAT(a.data_afastamento, '%d/%m/%Y') data_afastamento_format, a.data_retorno, DATE_FORMAT(a.data_retorno, '%d/%m/%Y') data_retorno_format, a.num_dias, a.cid, a.observacao
    FROM afastamentos a
    JOIN rl_colaboradores_empresas rl ON (a.id_rl_colaborador_empresa = rl.id_rl_colaborador_empresa)
    WHERE a.ativo = 1
    $where
    ORDER BY a.data_entrega DESC
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // DATA RETORNO
            if($row->num_dias == 0 && $row->data_retorno_format == '00/00/0000') {
                $row->data_retorno_format = 'Indefinido';
            } elseif($row->data_retorno_format == '00/00/0000') {
                $row->data_retorno_format = '';
            }

            $dados[] = $row;
        }


        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum colaborador foi encontrado'
        ));
    }

    echo $result;
    exit;
}
