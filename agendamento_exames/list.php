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
            AND rl.id_rl_agendamento_exame = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    } elseif ($id_agendamento > 0) {
        $where = "
            AND rl.id_agendamento = " . mysqli_real_escape_string($conecta, $id_agendamento) . "
        ";
    }

    $sql = "
        SELECT rl.id_exame,
        e.procedimento, e.cod
        FROM rl_agendamento_exames rl
        LEFT JOIN exames e ON e.id_exame = rl.id_exame
        WHERE rl.ativo = '1' 
        $where
        ORDER BY e.procedimento
        ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        while ($row = mysqli_fetch_object($query)) {
            // FORMATAR NOME DO PROCEDIMENTO COM CÓD ESOCIAL
            if (!empty($row->cod)) {
                $row->procedimento_format = $row->procedimento . ' | eSocial: ' . $row->cod;
            } else {
                $row->procedimento_format = $row->procedimento;
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
