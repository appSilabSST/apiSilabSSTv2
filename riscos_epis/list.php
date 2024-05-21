<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_rl_setor_risco = $postjson['id_rl_setor_risco'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_risco_epi = $id
        ";
    }

    // SE TIVER ID_SETOR_RISCO PARA BUSCA
    if ($id_rl_setor_risco > 0) {
        $where .= "
        AND rl.id_rl_setor_risco = $id_rl_setor_risco
        ";
    }

    $sql = "
    SELECT rl.id_rl_risco_epi , rl.id_rl_setor_risco , rl.id_epi , rl.ca ,
    e.grupo , e.epi , CONCAT_WS(' - ' , e.grupo , e.epi) grupo_epi
    FROM rl_riscos_epis rl
    JOIN rl_setores_riscos rl2 ON (rl.id_rl_setor_risco = rl2.id_rl_setor_risco)
    JOIN epis e ON (rl.id_epi = e.id_epi)
    WHERE rl.ativo = 1
    $where
    ORDER BY e.grupo , e.epi
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
            'result' => 'Nenhum registro foi encontrado.'
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
