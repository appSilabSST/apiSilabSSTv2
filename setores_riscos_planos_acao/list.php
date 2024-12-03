<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_pgr = $postjson['id_pgr'];
    $id_setor = $postjson['id_setor'];
    $id_rl_setor_risco = $postjson['id_rl_setor_risco'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_setor_risco_plano_acao = $id
        ";
    }

    // SE TIVER ID_PGR PARA BUSCA
    if ($id_pgr > 0) {
        $where .= "
        AND rl.id_pgr = $id_pgr
        ";
    }

    // SE TIVER ID_RL_SETOR_RISCO PARA BUSCA
    if ($id_rl_setor_risco > 0) {
        $where .= "
        AND rl.id_rl_setor_risco = $id_rl_setor_risco
        ";
    }

    $sql = "
    SELECT rl.id_rl_setor_risco_plano_acao , rl.id_rl_setor_risco , rl.data_avaliacao , DATE_FORMAT(rl.data_avaliacao, '%d/%m/%Y') data_avaliacao_mask , rl.plano_acao , rl.descricao , rl.medida_suficiente , rl.indicacao_medida ,
    s.setor ,
    r.descricao agente_nocivo , r.cod
    FROM rl_setores_riscos_planos_acao rl
    JOIN rl_setores_riscos rl1 ON (rl.id_rl_setor_risco = rl1.id_rl_setor_risco)
    JOIN setores s ON (rl1.id_setor = s.id_setor)
    JOIN riscos r ON (r.id_risco = rl1.id_risco)
    WHERE rl.ativo = 1
    $where
    ORDER BY s.setor , r.descricao , rl.plano_acao
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            // AGENTE NOCIVO
            if (!empty($row->cod)) {
                $row->agente_nocivo = $row->agente_nocivo . ' | eSocial: ' . $row->cod;
            }

            // SETOR | AGENTE NOCIVO
            if (!empty($row->cod)) {
                $row->setor_risco = $row->setor . ' - ' . $row->agente_nocivo;
            }

            // MEDIDA SUFICIENTE
            if ($row->medida_suficiente == 1) {
                $row->medida_suficiente_mask = '
                    <div class="alert mb-0 text-center alert-success">
                        Sim
                    </div>';
            } else {
                $row->medida_suficiente_mask = '
                    <div class="alert mb-0 text-center alert-danger">
                        Não
                    </div>';
            }


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
