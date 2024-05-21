<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson["id"]);
$form = $postjson['form'];

//LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar') {

  $id_local_atividade = trim($form["id_local_atividade"]);
  $setor = trim($form["setor"]);
  $descricao = trim($form["descricao"]);
  $conclusao = trim($form["conclusao"]);
  $status = trim($form["status"]);

  // VERIFICA SE ESTÁ VINDO CONCLUSÃO DO LTCAT
  if (!empty($conclusao) && $id > 0) {
    $sql = "
      UPDATE setores SET
      conclusao = '" . mysqli_real_escape_string($conecta, $conclusao) . "',
      data_edit = NOW()
      WHERE id_setor = $id
    ";
  } elseif ($id > 0) {
    $sql = "
      UPDATE setores SET
      setor = '" . mysqli_real_escape_string($conecta, $setor) . "',
      descricao = '" . mysqli_real_escape_string($conecta, $descricao) . "',
      status = '" . mysqli_real_escape_string($conecta, $status) . "',
      data_edit = NOW()
      WHERE id_setor = $id
      ";
  } else {
    $sql = "
      INSERT INTO setores (id_local_atividade, setor, descricao, status) VALUES 
      (
        '" . mysqli_real_escape_string($conecta, $id_local_atividade) . "', 
        '" . mysqli_real_escape_string($conecta, $setor) . "', 
        '" . mysqli_real_escape_string($conecta, $descricao) . "',
        '" . mysqli_real_escape_string($conecta, $status) . "'
      )
      ";
  }

  // echo $sql;exit;
  $query = mysqli_query($conecta, $sql);

  if ($query) {

    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso.'
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar salvar registro.'
    ));
  }

  echo $result;
}
