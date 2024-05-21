<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

//LISTAGEM DR REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
        AND e.id_exame = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    $sql = "
    SELECT e.id_exame, e.procedimento, e.valor_custo, e.valor_cobrar, e.cod , e.validade, e.padronizar,
    f.razao_social as nome_fornecedor, f.id_fornecedor
    FROM exames e 
    LEFT JOIN fornecedores f on f.id_fornecedor = e.id_fornecedor             
    WHERE e.ativo = 1
    $where
    ORDER BY procedimento";

    $query  = mysqli_query($conecta, $sql);
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

        if ($row->validade == 0) {
            $row->validade_mask = "Indeterminado";
        } elseif ($row->validade == 1) {
            $row->validade_mask = "01 mês";
        } else {
            $row->validade_mask = str_pad($row->validade, 2, 0, STR_PAD_LEFT) . " meses";
        }

        // FORMATAR NOME DO PROCEDIMENTO COM CÓD ESOCIAL
        if (!empty($row->cod)) {
            $row->procedimento_format = $row->procedimento . ' | eSocial: ' . $row->cod;
        } else {
            $row->procedimento_format = $row->procedimento;
        }

        $row->valor_custo_mask = "R$ " . number_format($row->valor_custo, 2, ',', '.');
        $row->valor_cobrar_mask = "R$ " . number_format($row->valor_cobrar, 2, ',', '.');
        $dados[] = $row;
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
