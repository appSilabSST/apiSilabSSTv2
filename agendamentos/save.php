<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];
$lista_exames = $postjson['lista_exames'];
$lista_riscos = $postjson['lista_riscos'];

// LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form) && !empty($lista_exames)) {

    $id = mysqli_real_escape_string($conecta, $id);
    // $data = mysqli_real_escape_string($conecta, trim($form["data"]));
    // $horario = mysqli_real_escape_string($conecta, trim($form["horario"]));
    $id_rl_colaborador_empresa = mysqli_real_escape_string($conecta, trim($form["id_rl_colaborador_empresa"]));
    $id_tipo_atendimento = mysqli_real_escape_string($conecta, trim($form["id_tipo_atendimento"]));
    $id_rl_setor_funcao = mysqli_real_escape_string($conecta, trim($form["id_rl_setor_funcao"]));

    foreach ($lista_exames as $key => $value) {
        if (
            $id_tipo_atendimento == 1 && $value['admissional'] == 1 ||
            $id_tipo_atendimento == 2 && $value['periodico'] == 1 ||
            $id_tipo_atendimento == 3 && $value['retorno_trabalho'] == 1 ||
            $id_tipo_atendimento == 4 && $value['mudanca_risco'] == 1 ||
            $id_tipo_atendimento == 5 && $value['monitoracao_pontual'] == 1 ||
            $id_tipo_atendimento == 6 && $value['demissional'] == 1
        ) {
            // Mantém o exame na lista
        } else {
            // Remove o exame da lista
            unset($lista_exames[$key]);
        }
    }

    // SE FOR MUDANÇA DE RISCO OU FUNÇÃO
    $update;
    if ($id_rl_setor_funcao > 0) {
        $sql = "
        UPDATE agendamentos SET
        id_rl_colaborador_empresa = '" . $id_rl_colaborador_empresa . "',
        id_tipo_atendimento = '" . $id_tipo_atendimento . "'
        WHERE id_agendamento = " . $id . ";
        UPDATE rl_colaboradores_empresas SET
        id_rl_setor_funcao = " . $id_rl_setor_funcao . "
        WHERE id_rl_colaborador_empresa = '" . $id_rl_colaborador_empresa . "';
        ";
    } else {
        $sql = "
        UPDATE agendamentos SET
        id_rl_colaborador_empresa = '" . $id_rl_colaborador_empresa . "',
        id_tipo_atendimento = '" . $id_tipo_atendimento . "'
        WHERE id_agendamento = " . $id;
    }


    // echo $sql;exit;
    $query  = mysqli_multi_query($conecta, $sql);
    // mysqli_close($conecta);
    // include('../conexao.php');

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
}
echo $result;
