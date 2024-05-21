<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR 
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $danos_saude = trim($form["danos_saude"]);

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE riscos SET
      danos_saude = '" . mysqli_real_escape_string($conecta, $danos_saude) . "'
      WHERE id_risco = " . mysqli_real_escape_string($conecta, $id);

  }

  // echo $sql;exit;
  $query  = mysqli_query($conecta, $sql);

  if ($query) {

    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso.'
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar salvar registro'
    ));
  }

  echo $result;
}
