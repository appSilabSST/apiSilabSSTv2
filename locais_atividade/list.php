<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = $postjson['id'];
    $id_empresa = $postjson['id_empresa'];

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
            AND l.id_local_atividade = $id
            ";
    }

    // SE TIVER ID_EMPRESA PARA BUSCA
    if ($id_empresa > 0) {
        $where .= "
             AND e.id_empresa = $id_empresa
             ";
    }

    $sql = "
        SELECT l.*, 
        t.tipo_ambiente,
        e.grau_risco AS grau_risco_empresa,e.razao_social AS empresa
        FROM locais_atividade l
        LEFT JOIN tipo_ambiente t ON (t.id_tipo_ambiente = l.id_tipo_ambiente)
        LEFT JOIN empresas e ON (l.id_empresa = e.id_empresa)
        WHERE l.ativo = '1'
        $where
        ORDER BY e.razao_social , l.razao_social
        ";

    $query  = mysqli_query($conecta, $sql);

    $count = 0;
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {

            $row->id = ++$count;

            // FORMATAR NR_INSCRICAO
            if (!empty($row->nr_inscricao)) {
                if ($row->tipo_inscricao == 1) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
                } elseif ($row->tipo_inscricao == 3) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '/' . substr($row->nr_inscricao, 9, 3) . '-' . substr($row->nr_inscricao, 12, 2);
                } elseif ($row->tipo_inscricao == 4) {
                    $row->nr_inscricao_mask = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 5) . '-' . substr($row->nr_inscricao, 10, 2);
                }
            }

            // VERIFICAR GRAU RISCO PREDOMINANTE
            // if($row->grau_risco >= $row->grau_risco_empresa) {
            //     $row->grau_risco_pred = $row->grau_risco;
            // } else {
            //     $row->grau_risco_pred = $row->grau_risco_empresa;
            // }

            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum local de atividade encontrada'
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
            'success' => false
        ));
    }

    echo $result;
    exit;
}
