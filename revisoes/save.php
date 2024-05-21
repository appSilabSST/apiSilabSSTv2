<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR 
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

    // RECUPERANDO INFORMAÇÕES
    $revisao = trim($form["revisao"]);
    $descricao = trim($form["descricao"]);

    // SE TIVER ID - EDITAR CADASTRO EXISTENTE
    if ($id > 0) {

        $sql = "
        UPDATE revisoes SET
        revisao = '" . mysqli_real_escape_string($conecta, $revisao) . "',
        descricao = '" . mysqli_real_escape_string($conecta, $descricao) . "'
        WHERE id_revisao = " . mysqli_real_escape_string($conecta, $id) ."
        AND status = 1
        AND ativo = 1
        ";

    }

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if ($query) {

        $result = json_encode(array(
            'success' => true,
            'result' => 'Registro salvo com sucesso.'
        ));
    } else {

        $result = json_encode(array(
            'success' => false,
            'result' => 'Falha ao tentar salvar registro'
        ));
    }

    echo $result;
}
