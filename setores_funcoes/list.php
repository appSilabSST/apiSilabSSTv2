<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_empresa = $postjson['id_empresa'];
    $id_setor = $postjson['id_setor'];
    $id_local_atividade = $postjson['id_local_atividade'];
    $id_rl_colaborador_empresa = $postjson['id_rl_colaborador_empresa'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND r.id_rl_setor_funcao = $id
        ";
    }

    // SE TIVER ID_SETOR PARA BUSCA
    if ($id_setor > 0) {
        $where .= "
        AND r.id_setor = $id_setor
        ";
    }

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_local_atividade > 0) {
        $where .= "
        AND s.id_local_atividade = $id_local_atividade
        ";
    }

    // SE TIVER id_rl_colaborador_empresa PARA BUSCA
    if ($id_rl_colaborador_empresa > 0) {
        $where .= "
        AND s.id_local_atividade = (
            SELECT id_local_atividade
            FROM rl_colaboradores_empresas
            WHERE id_rl_colaborador_empresa = $id_rl_colaborador_empresa
        )
        ";
    }

    $sql = "
    SELECT r.id_rl_setor_funcao,r.cbo,r.funcao,r.descricao,r.jornada_trabalho,r.qtd_funcionarios,
    s.id_setor,s.setor,
    la.id_local_atividade,la.razao_social
    FROM rl_setores_funcoes AS r
    INNER JOIN setores AS s ON (r.id_setor = s.id_setor)
    INNER JOIN locais_atividade AS la ON (s.id_local_atividade = la.id_local_atividade)
    WHERE r.ativo = 1
    $where
    ORDER BY s.setor,r.funcao
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
            'result' => 'Nenhum Função foi encontrada'
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
