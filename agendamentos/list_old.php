<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);
$dados = array();

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {

    $id = trim($postjson['id']);
    $data_agenda = trim($postjson['data_agenda']) ?? date("Y-m-d");

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
        AND id_agenda = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    }

    // LISTA AGENDAMENTOS JÁ REALIZADOS
    // (
    //     SELECT ra.qtde - COUNT(a.horario)
    //     FROM regras_agendamento ra
    //     WHERE STR_TO_DATE(a.horario,'%H:%i') BETWEEN STR_TO_DATE(ra.horario_inicio,'%H:%i') AND STR_TO_DATE(ra.horario_fim,'%H:%i')
    //     AND DATE_FORMAT('$data_agenda', '%w') + 1 = ra.id_dia_semana
    //     AND STR_TO_DATE('$data_agenda', '%Y-%m-%d') BETWEEN STR_TO_DATE(ra.data_inicio, '%Y-%m-%d') AND IF(ra.data_fim = '0000-00-00', STR_TO_DATE('2100-01-01', '%Y-%m-%d'), STR_TO_DATE(ra.data_fim, '%Y-%m-%d')
    // )
    // GROUP BY a.horario
    // ) qtde_disponivel
    $sql = "
    SELECT a.id_agenda, a.nr_agenda, a.data, DATE_FORMAT(a.data, '%d/%m/%Y') data_format, a.horario, DATE_FORMAT(a.horario, '%H:%i') horario_format, 
    c.cpf, c.nome nome_colaborador, 
    e.razao_social, 
    ta.tipo_atendimento
    FROM agenda a
    JOIN colaboradores c ON (a.id_colaborador = c.id_colaborador)
    JOIN empresas e ON (a.id_empresa = e.id_empresa)
    JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
    WHERE a.ativo = '1' 
    AND DATE_FORMAT(a.data, '%Y-%m-%d') = '$data_agenda'
    ORDER BY a.data,
    a.horario
    ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        while ($row = mysqli_fetch_object($query)) {
            $dados[] = $row;
        }
    }

    
    // CONTA QTOS ATENDIMENTOS JÁ AGENDADOS POR HORÁRIO
    $horarios_agendados = array_count_values(array_column($dados, 'horario_format')) ?? array();
    // echo $horarios_agendados;exit;

    // MONTA A AGENDA VIRTUALMENTE DE ACORDO COM AS REGRAS DE AGENDAMENTO
    $sql = "
    SELECT *
    FROM regras_agendamento ra
    WHERE DATE_FORMAT('$data_agenda', '%w') + 1 = ra.id_dia_semana
    ";

    $query  = mysqli_query($conecta, $sql);
    $row2 = array();

    if (mysqli_num_rows($query) > 0) {

        while ($row = mysqli_fetch_object($query)) {

            $qtde_disponivel = 0;
            $novo_horario = date('H:i', strtotime($row->horario_inicio));

            while (strtotime($novo_horario) <= strtotime($row->horario_fim)) {

                if (array_key_exists($novo_horario, $horarios_agendados)) {
                    $qtde_disponivel = $row->qtde - $horarios_agendados[$novo_horario];
                    $qtde_disponivel < 0 ? $qtde_disponivel = 0 : $qtde_disponivel = $qtde_disponivel;
                } else {
                    $qtde_disponivel = $row->qtde;
                }

                // CRIA HORÁRIOS DE AGENDA VIRTUALMENTE
                for ($i = 0; $i < $qtde_disponivel; $i++) {
                    $row2['horario_format'] = $novo_horario;
                    $dados[] = $row2;
                }

                $novo_horario = date('H:i', strtotime("+ $row->intervalo minutes", strtotime($novo_horario)));
            }
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
