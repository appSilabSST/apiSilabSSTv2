<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND  mp.id_meio_propagacao = ".mysqli_real_escape_string($conecta,$id)."
        ";
    }

    $sql = "
        SELECT mp.*
        FROM meios_propagacao mp
        WHERE mp.ativo = '1'
        $where
        ORDER BY mp.id_meio_propagacao
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {
            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum registro encontrado'
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
