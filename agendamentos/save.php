<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];
$lista_exames = $postjson['lista_exames'];
$lista_riscos = $postjson['lista_riscos'];

// LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

    $id = mysqli_real_escape_string($conecta, $id);
    $id_pcmso = mysqli_real_escape_string($conecta, trim($form["id_pcmso"]));
    $id_rl_colaborador_empresa = mysqli_real_escape_string($conecta, trim($form["id_rl_colaborador_empresa"]));
    $id_tipo_atendimento = mysqli_real_escape_string($conecta, trim($form["id_tipo_atendimento"]));
    $id_rl_setor_funcao = mysqli_real_escape_string($conecta, trim($form["id_rl_setor_funcao"]));

    $sql = "
    UPDATE agendamentos SET
    id_pcmso = CASE WHEN nr_agendamento > 0 THEN id_pcmso ELSE '" . $id_pcmso . "' END,
    id_rl_setor_funcao = CASE WHEN nr_agendamento > 0 THEN id_rl_setor_funcao ELSE '" . $id_rl_setor_funcao . "' END,
    id_rl_colaborador_empresa = CASE WHEN nr_agendamento > 0 THEN id_rl_colaborador_empresa ELSE '" . $id_rl_colaborador_empresa . "' END,
    id_tipo_atendimento = '" . $id_tipo_atendimento . "'
    WHERE id_agendamento = " . $id . "
    ";

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if (!empty($lista_exames) && !empty($lista_riscos)) {
        $sql = "
        DELETE a, r
        FROM rl_agendamento_exames a
        JOIN rl_agendamento_riscos r ON a.id_agendamento = r.id_agendamento
        WHERE a.id_agendamento = $id
        ";

        // echo $sql;exit;
        $query  = mysqli_query($conecta, $sql);

        $sql = "
        INSERT INTO rl_agendamento_exames (id_agendamento, id_exame, data) VALUES
        ";

        foreach ($lista_exames as $key => $value) {
            $sql .= "($id, " . $value['id_exame'] . ", '" . $value['data'] . "'),";
            // if (
            //     $id_tipo_atendimento == 1 && $value['admissional'] == 1 ||
            //     $id_tipo_atendimento == 2 && $value['periodico'] == 1 ||
            //     $id_tipo_atendimento == 3 && $value['retorno_trabalho'] == 1 ||
            //     $id_tipo_atendimento == 4 && $value['mudanca_risco'] == 1 ||
            //     $id_tipo_atendimento == 5 && $value['monitoracao_pontual'] == 1 ||
            //     $id_tipo_atendimento == 6 && $value['demissional'] == 1
            // ) {
            //     // Mantém o exame na lista
            // } else {
            //     // Remove o exame da lista
            //     unset($lista_exames[$key]);
            // }
        }

        $sql = substr($sql, 0, -1);
        // echo $sql;exit;
        $query  = mysqli_query($conecta, $sql);

        $sql = "
        INSERT INTO rl_agendamento_riscos (id_agendamento, id_risco) VALUES
        ";

        foreach ($lista_riscos as $key => $value) {
            $sql .= "($id, " . $value['id_risco'] . "),";
            // if (
            //     $id_tipo_atendimento == 1 && $value['admissional'] == 1 ||
            //     $id_tipo_atendimento == 2 && $value['periodico'] == 1 ||
            //     $id_tipo_atendimento == 3 && $value['retorno_trabalho'] == 1 ||
            //     $id_tipo_atendimento == 4 && $value['mudanca_risco'] == 1 ||
            //     $id_tipo_atendimento == 5 && $value['monitoracao_pontual'] == 1 ||
            //     $id_tipo_atendimento == 6 && $value['demissional'] == 1
            // ) {
            //     // Mantém o exame na lista
            // } else {
            //     // Remove o exame da lista
            //     unset($lista_exames[$key]);
            // }
        }

        $sql = substr($sql, 0, -1);
        // echo $sql;exit;
        $query  = mysqli_query($conecta, $sql);
    }


    if ($query) {
        $result = json_encode(array(
            'success' => true,
            'result' => 'Registro salvo com sucesso.'
        ));
    } else {
        $result = json_encode(array(
            'success' => false,
            'result' => 'Falha ao tentar editar registro'
        ));
    }
} elseif ($postjson['requisicao'] == 'reservar') {

    // Realizar a numeracao do documento
    $rs = mysqli_query($conecta, "select IFNULL(max(nr_agendamento),0) as valor from agendamentos");
    if ($row = mysqli_fetch_object($rs)) {
        $max = $row->valor;
        if (($max - (intval(gmdate("y")) * 100000)) >= 0) {
            $nr_agendamento = $max + 1;
        } else {
            $nr_agendamento = intval(gmdate("y")) * 100000 + 1;
        }
    }

    $sql = "
    UPDATE agendamentos SET
    nr_agendamento = '" . $nr_agendamento . "',
    disponivel = 0
    WHERE id_agendamento = " . $id;

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if ($query) {

        $result = json_encode(array(
            'success' => true
        ));
    } else {

        $result = json_encode(array(
            'success' => false
        ));
    }
} elseif ($postjson['requisicao'] == 'cancelar') {

    $sql = "
    UPDATE agendamentos SET
    cancelado = 1
    WHERE id_agendamento = " . $id;

    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);

    if ($query) {

        $result = json_encode(array(
            'success' => true
        ));
    } else {

        $result = json_encode(array(
            'success' => false
        ));
    }
}
echo $result;
