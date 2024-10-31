<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

    try {
        $id = mysqli_real_escape_string($conecta, $id);
        $id_agendamento = mysqli_real_escape_string($conecta, trim($form["id_agendamento"]));
        $data = mysqli_real_escape_string($conecta, trim($form["data"]));
        $id_exame = mysqli_real_escape_string($conecta, trim($form["id_exame"]));

        if (!empty($id)) {
            $sql = "
            INSERT INTO rl_agendamento_exames (id_agendamento , id_exame , data) VALUES 
            (
                '" . $id_agendamento . "',
                '" . $id_exame . "',
                (
                    SELECT data
                    FROM agendamentos
                    WHERE id_agendamento = '" . $id_agendamento . "'
                )
            )
            ";
        } else {
            $sql = "
            UPDATE rl_agendamento_exames SET
            id_exame = '" . $id_exame . "',
            data = '" . $data . "'
            WHERE id_rl_agendamento_exame = '" . $id . "'
            ";
        }

        // echo $sql;exit;
        $query  = mysqli_query($conecta, $sql);
        $result = json_encode(array(
            'success' => true,
            'result' => 'Registro salvo com sucesso.'
        ));
    } catch (Exception $ex) {
        if ($ex->getCode() == 1062) {
            $msg = "Esse exame já está vinculado a este agendamento.";
        } else {
            $msg = $ex->getCode() . " - " . $ex->getMessage();
        }
        $result = json_encode(array(
            'success' => false,
            'result' => $msg
        ));
    }
}
echo $result;
