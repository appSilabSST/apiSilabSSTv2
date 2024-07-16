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
        $id_risco = mysqli_real_escape_string($conecta, trim($form["id_risco"]));
    
        $sql = "
        INSERT INTO rl_agendamento_riscos (id_agendamento , id_risco) VALUES 
        (
            '" . $id_agendamento . "',
            '" . $id_risco . "'
        )
        ";
    
        // echo $sql;exit;
        $query  = mysqli_query($conecta, $sql);
        $result = json_encode(array(
            'success' => true,
            'result' => 'Registro salvo com sucesso.'
        ));
    } catch (Exception $ex) {
        if($ex->getCode() == 1062) {
            $msg = "Esse risco já está vinculado a este agendamento.";
        } else {
            $msg = $ex->getCode() ." - " . $ex->getMessage();
        }
        $result = json_encode(array(
            'success' => false,
            'result' => $msg
        ));
    }

}
echo $result;
