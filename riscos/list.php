<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

//LISTAGEM DR REGISTROS
if ($postjson['requisicao'] == 'listar') {

    $id = trim($postjson['id']);
    $agente_nocivo = trim($postjson['agente_nocivo']);
    $id_lcat = trim($postjson['id_lcat']);


    if ($id > 0) {
        $where .= "
        AND id_risco = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    if (!empty($agente_nocivo)) {
        $where .= "
        AND IF(
            cod = '',
            descricao LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "',
            CONCAT(descricao, ' | eSocial: ', cod) LIKE '" . mysqli_real_escape_string($conecta, $agente_nocivo) . "'
        )
        ";
    }

    // PARA LTCAT PUXAR SOMENTE RISCO ESOCIAL
    if ($id_lcat > 0) {
        $where .= "
        AND (cod IS NOT NULL OR cod <> '')
        ";
    }

    $sql = "
    SELECT id_risco, cod, descricao, grupo, cor, danos_saude
    FROM riscos
    WHERE ativo = '1'
    $where
    ORDER BY descricao
    ";

    // echo $sql;
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

            $bg = '';
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

            if (!empty($row->cod)) {
                $row->agente_nocivo = $row->descricao . ' | eSocial: ' . $row->cod;
            } else {
                $row->agente_nocivo = $row->descricao;
            }

            $dados[] = $row;
        }

        if ($query) {
            $result = json_encode(array(
                'success' => true,
                'result' => $dados
            ));
        } else {
            $result = json_encode(array(
                'success' => false,
                'result' => 'Falha ao tentar comunicação com Banco de Dados'
            ));
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum registro foi encontrado.'
        ));
    }
    echo $result;
    exit;
}
