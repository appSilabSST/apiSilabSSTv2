<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $padronizar = trim($form['padronizar']);
  $valor_cobrar = str_replace(",", ".", trim($form['valor_cobrar']));
  $valor_custo = str_replace(",", ".", trim($form['valor_custo']));
  $validade = trim($form["validade"]);
  $id_fornecedor = trim($form['id_fornecedor']);

  $sql = "
    UPDATE exames SET
    padronizar = '" . mysqli_real_escape_string($conecta, $padronizar) . "',
    valor_custo = '" . mysqli_real_escape_string($conecta, $valor_custo) . "',
    valor_cobrar = '" . mysqli_real_escape_string($conecta, $valor_cobrar) . "',
    validade = '" . mysqli_real_escape_string($conecta, $validade) . "',
    id_fornecedor = '" . mysqli_real_escape_string($conecta, $id_fornecedor) . "'
    WHERE id_exame = " . mysqli_real_escape_string($conecta, $id) . "
  ";

  // echo $sql; exit;
  $query  = mysqli_query($conecta, $sql);

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

  echo $result;
}
