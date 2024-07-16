<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);
$horarios_disponiveis = array();

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {

    $id = trim($postjson['id']);
    $id_agendamento = trim($postjson['id_agendamento']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND rl.id_rl_agendamento_risco = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    } elseif ($id_agendamento > 0) {
        $where = "
            AND rl.id_agendamento = " . mysqli_real_escape_string($conecta, $id_agendamento) . "
        ";
    }

    $sql = "
        SELECT rl.id_risco,
        r.cod, r.descricao
        FROM rl_agendamento_riscos rl
        LEFT JOIN riscos r ON r.id_risco = rl.id_risco
        WHERE rl.ativo = '1' 
        $where
        ORDER BY r.descricao
        ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        while ($row = mysqli_fetch_object($query)) {
            // FORMATAR NOME DO RISCO COM CÓD ESOCIAL
            if (!empty($row->cod)) {
                $row->agente_nocivo = $row->descricao . ' | eSocial: ' . $row->cod;
            } else {
                $row->agente_nocivo = $row->descricao;
            }

            $dados[] = $row;
        }

        $result = json_encode(array(
            'success' => true,
            'result' => $dados
        ));
    } else {

        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhum agendamento foi encontrado'
        ));
    }

    echo $result;
    exit;
}
