<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
        AND pa.id_plano_acao = $id
        ";
    }

    $sql = "
    SELECT pa.* ,
    r.descricao descricao_risco, r.cod
    FROM planos_acao pa
    LEFT JOIN riscos r ON (pa.id_risco = r.id_risco)
    WHERE pa.ativo = 1
    $where
    ORDER BY pa.plano_acao
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            if ($row->padronizar == 1) {
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

            // AGENTE NOCIVO AUTO-COMPLETE
            if (!empty($row->cod)) {
                $row->agente_nocivo = $row->descricao_risco . ' | eSocial: ' . $row->cod;
            } else {
                $row->agente_nocivo = $row->descricao_risco;
            }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum Plano de Ação foi encontrado.'
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
