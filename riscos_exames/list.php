<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_risco = $postjson['id_risco'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND rl.id_rl_risco_exame = $id
        ";
    }

    // SE TIVER ID PARA BUSCA
    if ($id_risco > 0) {
        $where .= "
        AND rl.id_risco = $id_risco
        ";
    }

    $sql = "
    SELECT rl.*,
    e.procedimento , e.cod , e.procedimento , CONCAT_WS(' | eSocial: ', e.procedimento , e.cod) procedimento_format
    FROM rl_riscos_exames AS rl
    JOIN exames e ON (rl.id_exame = e.id_exame)
    WHERE rl.ativo = 1
    $where
    ORDER BY e.procedimento
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {
            $tipos_avaliacao = array();
            $tipos_avaliacao_select = array();

            if($row->padronizar == 1) {
                $row->padronizar = true;
                $row->padronizar_mask = '
                    <div class="alert mb-0 alert-success text-center" role="alert">
                    Ativo
                    </div>';
            } else {
                $row->padronizar = false;
                $row->padronizar_mask = '
                    <div class="alert mb-0 alert-danger text-center" role="alert">
                    Inativo
                    </div>';
            }

            if ($row->admissional == 1) {
                $tipos_avaliacao[] = 'Admissional';
                $tipos_avaliacao_select[] = 'admissional';
            }
            if ($row->periodico == 1) {
                $tipos_avaliacao[] = 'Periódico';
                $tipos_avaliacao_select[] = 'periodico';
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

            if($row->periodicidade == 0) {
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
