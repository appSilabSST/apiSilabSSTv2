<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR 
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $id_proposta = $form["id_proposta"];
  $id_servico = $form["id_servico"];
  $valor = trim($form["valor"]);
  $prazo = trim(str_replace('.', ',', $form["prazo"]));
  $observacoes = trim($form["observacoes"]);

  // VERIFICA SE EXAME JÁ NÃO ESTÁ VINCULADO AO SETOR
  if ($id > 0) {
    $where = "
      AND id_rl_proposta_servico <> $id
      ";
  } else {
    $where = "";
  }

  $sql = "
    SELECT id_rl_proposta_servico
    FROM rl_propostas_servicos
    WHERE id_proposta = '" . mysqli_real_escape_string($conecta, $id_proposta) . "'
    AND id_servico = '" . mysqli_real_escape_string($conecta, $id_servico) . "'
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
      UPDATE rl_propostas_servicos SET
      id_servico = '" . mysqli_real_escape_string($conecta, $id_servico) . "',
      valor = '" . mysqli_real_escape_string($conecta, $valor) . "',
      prazo = '" . mysqli_real_escape_string($conecta, $prazo) . "',
      observacoes = '" . mysqli_real_escape_string($conecta, $observacoes) . "'
      WHERE id_rl_proposta_servico = " . mysqli_real_escape_string($conecta, $id);
  } else {
    $sql = "
    INSERT INTO rl_propostas_servicos (id_proposta , id_servico , valor , prazo , observacoes) VALUES 
    (
      '" . mysqli_real_escape_string($conecta, $id_proposta) . "',
      '" . mysqli_real_escape_string($conecta, $id_servico) . "',
      '" . mysqli_real_escape_string($conecta, $valor) . "',
      '" . mysqli_real_escape_string($conecta, $prazo) . "',
      '" . mysqli_real_escape_string($conecta, $observacoes) . "'
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
