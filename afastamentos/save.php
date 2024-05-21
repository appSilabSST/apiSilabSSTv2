<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $id = mysqli_real_escape_string($conecta, $id);
  $id_rl_colaborador_empresa = mysqli_real_escape_string($conecta, trim($form["id_rl_colaborador_empresa"]));
  $data_entrega = mysqli_real_escape_string($conecta, trim($form["data_entrega"]));
  $data_afastamento = mysqli_real_escape_string($conecta, trim($form["data_afastamento"]));
  $cid = mysqli_real_escape_string($conecta, trim(strtoupper($form["cid"])));
  $num_dias = mysqli_real_escape_string($conecta, trim($form["num_dias"]));
  $data_retorno = mysqli_real_escape_string($conecta, trim($form["data_retorno"]));
  $observacao = mysqli_real_escape_string($conecta, trim($form["observacao"]));

  if ($id > 0) {

    $sql = "
    UPDATE afastamentos SET
    data_entrega = '" . $data_entrega . "',
    data_afastamento = '" . $data_afastamento . "',
    cid = '" . $cid . "',
    num_dias = '" . $num_dias . "',
    data_retorno = '" . $data_retorno . "',
    observacao = '" . $observacao . "'
    WHERE id_afastamento = " . $id;
  } else {

    $sql = "
    INSERT INTO afastamentos (id_rl_colaborador_empresa,data_entrega,data_afastamento,cid,num_dias,data_retorno,observacao) VALUES 
    (
      '" . $id_rl_colaborador_empresa . "',
      '" . $data_entrega . "', 
      '" . $data_afastamento . "',
      '" . $cid . "',
      '" . $num_dias . "',
      '" . $data_retorno . "',
      '" . $observacao . "'
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
      'result' => 'Falha ao tentar editar registro'
    ));
  }

  echo $result;
}
