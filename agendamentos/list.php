<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);
$horarios_disponiveis = array();

// LISTAGEM DE REGISTROS
if ($postjson['requisicao'] == 'listar') {

    $id = trim($postjson['id']);
    $data_agendamento = trim($postjson['data_agendamento']) ?? date("Y-m-d");

    // SE TIVER ID PARA BUSCA
    if ($id > 0) {
        $where = "
            AND a.id_agendamento = " . mysqli_real_escape_string($conecta, $id) . "
        ";
    } else {
        $where = "
            AND a.data = '" . mysqli_real_escape_string($conecta, $data_agendamento) . "'
        ";
    }

    $sql = "
        SELECT a.id_agendamento, a.id_rl_colaborador_empresa, a.id_rl_setor_funcao, a.nr_agendamento, a.id_pcmso, a.data, DATE_FORMAT(a.data, '%d/%m/%Y') data_format, a.horario, DATE_FORMAT(a.horario, '%H:%i') horario_format, a.cancelado,
        c.id_colaborador, c.cpf, c.nome nome_colaborador, 
        e.razao_social, 
        ta.id_tipo_atendimento, ta.tipo_atendimento,
        rl_sf.funcao
        FROM agendamentos a
        LEFT JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
        LEFT JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
        LEFT JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa)
        LEFT JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
        LEFT JOIN rl_setores_funcoes rl_sf ON (rl_ce.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
        WHERE a.ativo = '1' 
        $where
        ORDER BY a.data,
        a.horario
        ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        while ($row = mysqli_fetch_object($query)) {
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
