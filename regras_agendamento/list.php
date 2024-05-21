<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {
    $id = trim($postjson['id']);

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where .= "
            AND ra.id_regra_agendamento = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    $sql = "
        SELECT ra.*, DATE_FORMAT(ra.data_inicio, '%d/%m/%Y') data_inicio_format, DATE_FORMAT(ra.data_fim, '%d/%m/%Y') data_fim_format, DATE_FORMAT(ra.horario_inicio, '%H:%i') horario_inicio_format, DATE_FORMAT(ra.horario_fim, '%H:%i') horario_fim_format,
        ds.dia_semana
        FROM regras_agendamento ra
        JOIN dias_semana ds ON (ra.id_dia_semana = ds.id_dia_semana)
        WHERE ra.ativo = '1'
        $where
        ORDER BY ra.id_dia_semana
        ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_object($query)) {
            $dados[] = $row;
        }
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Nenhuma Regra de Agendamento foi encontrada'
        ));
        echo $result;
        exit;
    }
}

if ($query) {
    $result = json_encode(array(
        'success' => true,
        'result' => $dados,
    ));
} else {
    $result = json_encode(array(
        'success' => false
    ));
}

echo $result;
exit;
