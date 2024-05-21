<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_empresa = $postjson['id_empresa'];
    $id_setor = $postjson['id_setor'];
    $id_rl_setor_funcao = $postjson['id_rl_setor_funcao'];
    $id_local_atividade = $postjson['id_local_atividade'];
    $id_pgr = $postjson['id_pgr'];
    $id_ltcat = $postjson['id_ltcat'];
    $tipo_documento = $postjson['tipo_documento'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_setor_risco = $id
        ";
    }

    // SE TIVER ID_SETOR PARA BUSCA
    if ($id_setor > 0) {
        $where .= "
        AND s.id_setor = $id_setor
        ";
    }

    // SE TIVER ID_RL_SETOR_FUNCAO PARA BUSCA
    if ($id_rl_setor_funcao > 0) {
        $where .= "
        AND s.id_setor = (
            SELECT id_setor
            FROM rl_setores_funcoes
            WHERE id_rl_setor_funcao = $id_rl_setor_funcao
        )
        ";
    }

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_local_atividade > 0) {
        $where .= "
        AND s.id_local_atividade = $id_local_atividade
        ";
    }

    // SE TIVER ID_PGR PARA LISTAR OS PLANOS DE AÇÃO E EPI's
    if ($id_pgr > 0) {
        $where_pgr = "
        , $id_pgr id_pgr
        ";
    }

    // SE TIVER ID_LTCAT PARA IGNORAR LISTA EPI's
    if ($id_ltcat > 0) {
        $where_ltcat = "
        , $id_ltcat id_ltcat
        ";
    }

    // SE TIVER ID_LTCAT LISTAR SOMENTE RISCOS COM CÓDIGO ESOCIAL
    if ($tipo_documento == 'ltcat') {
        $where .= "
        AND LENGTH(r.cod) > 0
        ";
    }

    $sql = "
    SELECT rl.*,
    r.descricao , r.cod , r.grupo , r.cor ,
    s.id_setor,s.setor,
    lt.id_local_atividade,lt.razao_social,
    te.id_tipo_exposicao,te.tipo_exposicao,
    cr.id_classificacao_risco,cr.classificacao_risco,
    mp.id_meio_propagacao,mp.meio_propagacao
    $where_pgr
    $where_ltcat
    FROM rl_setores_riscos AS rl
    JOIN riscos r ON (rl.id_risco = r.id_risco)
    JOIN setores AS s ON (rl.id_setor = s.id_setor)
    JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
    LEFT JOIN tipos_exposicao AS te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
    LEFT JOIN classificacao_riscos AS cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
    LEFT JOIN meios_propagacao AS mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
    WHERE rl.ativo = 1
    $where
    ORDER BY s.setor,r.grupo,r.descricao
    ";

    // echo $sql;
    // exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // AGENTE NOCIVO AUTO-COMPLETE
            if (!empty($row->cod)) {
                $row->agente_nocivo = $row->descricao . ' | eSocial: ' . $row->cod;
            } else {
                $row->agente_nocivo = $row->descricao;
            }

            // COR NO TEXTO DO AGENTE
            if ($row->grupo == 'FÍSICOS') {
                $bg = 'bg-fisicos';
            } elseif ($row->grupo == 'QUÍMICOS') {
                $bg = 'bg-quimicos';
            } elseif ($row->grupo == 'BIOLÓGICOS') {
                $bg = 'bg-biologicos';
            } elseif ($row->grupo == 'ERGONÔMICOS') {
                $bg = 'bg-ergonomicos';
            } elseif ($row->grupo == 'ACIDENTES') {
                $bg = 'bg-acidentes';
            }

            $row->grupo_mask = '
                    <div class="alert mb-0 text-center ' . $bg . '">
                    ' . $row->grupo . '
                    </div>';

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Risco foi encontrado'
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
