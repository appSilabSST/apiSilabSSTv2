<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR 
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_proposta = $form["id_proposta"];
  $id_exame = $form["id_exame"];
  $valor = trim($form["valor"]);

  // VERIFICA SE EXAME JÁ NÃO ESTÁ VINCULADO A PROPOSTA
  if ($id > 0) {
    $where = "
      AND id_rl_proposta_exame <> $id
      ";
  } else {
    $where = "";
  }

  $sql = "
    SELECT id_rl_proposta_exame
    FROM rl_propostas_exames
    WHERE id_proposta = '" . mysqli_real_escape_string($conecta, $id_proposta) . "'
    AND id_exame = '" . mysqli_real_escape_string($conecta, $id_exame) . "'
    $where
    ";
  // echo $sql;exit;
  $query = mysqli_query($conecta, $sql);

  if (mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Este vínculo de dados já existe.'
    ));
    echo $result;
    exit;
  }

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE rl_propostas_exames SET
      id_exame = '" . mysqli_real_escape_string($conecta, $id_exame) . "',
      valor = '" . mysqli_real_escape_string($conecta, $valor) . "'
      WHERE id_rl_proposta_exame = " . mysqli_real_escape_string($conecta, $id);
  } else {
    $sql = "
    INSERT INTO rl_propostas_exames (id_proposta , id_exame , valor) VALUES 
    (
      '" . mysqli_real_escape_string($conecta, $id_proposta) . "',
      '" . mysqli_real_escape_string($conecta, $id_exame) . "',
      '" . mysqli_real_escape_string($conecta, $valor) . "'
    )
    ";
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
