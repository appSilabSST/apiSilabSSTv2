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
    $id_pcmso = $postjson['id_pcmso'];
    $tipo_documento = $postjson['tipo_documento'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_setor_exame = $id
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

    // SE TIVER id_rl_colaborador_empresa PARA BUSCA
    if ($id_rl_colaborador_empresa > 0) {
        $where .= "
        AND s.id_setor = (
            SELECT id_setor
            FROM rl_setores_funcoes rl_sf
            JOIN rl_colaboradores_empresas rl_ce ON rl_sf.id_rl_setor_funcao = rl_ce.id_rl_setor_funcao
            WHERE id_rl_colaborador_empresa = $id_rl_colaborador_empresa
        )
        ";
    }

    // SE TIVER id_local_atividade PARA BUSCA
    if ($id_local_atividade > 0) {
        $where .= "
        AND s.id_local_atividade = $id_local_atividade
        ";
    }

    $sql = "
    SELECT rl.*,
    e.procedimento , e.cod , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod) procedimento_format,
    s.id_setor,s.setor
    FROM rl_setores_exames AS rl
    JOIN exames e ON (rl.id_exame = e.id_exame)
    JOIN setores AS s ON (rl.id_setor = s.id_setor)
    JOIN locais_atividade AS lt ON (s.id_local_atividade = lt.id_local_atividade)
    WHERE rl.ativo = 1
    $where
    ORDER BY s.setor,e.procedimento
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {
            $tipos_avaliacao = array();
            $tipos_avaliacao_select = array();

            if ($row->admissional == 1) {
                $tipos_avaliacao[] = 'Admissional';
                $tipos_avaliacao_select[] = 'admissional';
            }
            if ($row->periodico == 1) {
                $tipos_avaliacao[] = 'Periódico';
                $tipos_avaliacao_select[] = 'periodico';
            }
            if ($row->monitoracao_pontual == 1) {
                $tipos_avaliacao[] = 'Monitoração Pontual';
                $tipos_avaliacao_select[] = 'monitoracao_pontual';
            }
            if ($row->mudanca_risco == 1) {
                $tipos_avaliacao[] = 'Mudança de Risco';
                $tipos_avaliacao_select[] = 'mudanca_risco';
            }
            if ($row->retorno_trabalho == 1) {
                $tipos_avaliacao[] = 'Retorno ao Trabalho';
                $tipos_avaliacao_select[] = 'retorno_trabalho';
            }
            if ($row->demissional == 1) {
                $tipos_avaliacao[] = 'Demissional';
                $tipos_avaliacao_select[] = 'demissional';
            }

            $row->tipos_avaliacao = implode(' , ', $tipos_avaliacao);
            $row->tipos_avaliacao_select = $tipos_avaliacao_select;

            if ($row->periodicidade == 0) {
                $row->periodicidade_format = '<div class="text-center"> - </div>';
            } elseif ($row->periodicidade == 1) {
                $row->periodicidade_format = '<div class="text-center"> 1 mês </div>';
            } else {
                $row->periodicidade_format = '<div class="text-center"> ' . $row->periodicidade . ' meses </div>';
            }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Exame foi encontrado.'
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
