<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);
    $id_proposta = trim($postjson['id_proposta']);
    $status = trim($postjson['status']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND rl.id_rl_proposta_servico = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // SE TIVER ID PROPOSTA PARA BUSCA
    if ($id_proposta > 0) {
        $where = "
            AND p.id_proposta = " . mysqli_real_escape_string($conecta, $id_proposta) . "
        ";
    }

    $sql = "
        SELECT rl.* ,
        s.servico
        FROM propostas p
        JOIN rl_propostas_servicos rl ON (p.id_proposta = rl.id_proposta)
        JOIN servicos s ON (s.id_servico = rl.id_servico)
        WHERE p.ativo = '1'
        $where
        ORDER BY s.servico
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // FORMATAR VALOR
            $row->valor_format = number_format($row->valor, 2, ',', '.');

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Serviço foi encontrado'
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
