<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson["id"]);
$form = $postjson["form"];

//LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $id_setor = trim($form["id_setor"]);
  $cbo = trim($form["cbo"]);
  $funcao = trim($form["funcao"]);
  $jornada_trabalho = trim($form["jornada_trabalho"]);
  $qtd_funcionarios = trim($form["qtd_funcionarios"]);
  $descricao = trim($form["descricao"]);

  if ($id > 0) {

    $sql = "
      UPDATE rl_setores_funcoes SET
      cbo = '" . mysqli_real_escape_string($conecta, $cbo) . "',
      funcao = '" . mysqli_real_escape_string($conecta, $funcao) . "',
      jornada_trabalho = '" . mysqli_real_escape_string($conecta, $jornada_trabalho) . "',
      qtd_funcionarios = '" . mysqli_real_escape_string($conecta, $qtd_funcionarios) . "',
      descricao = '" . mysqli_real_escape_string($conecta, $descricao) . "',
      data_edit = NOW()
      WHERE id_rl_setor_funcao = $id
      ";
  } else {

    $sql = "
      INSERT INTO rl_setores_funcoes (id_setor, cbo , funcao , jornada_trabalho , qtd_funcionarios , descricao) VALUES 
      (
        '" . mysqli_real_escape_string($conecta, $id_setor) . "', 
        '" . mysqli_real_escape_string($conecta, $cbo) . "', 
        '" . mysqli_real_escape_string($conecta, $funcao) . "', 
        '" . mysqli_real_escape_string($conecta, $jornada_trabalho) . "', 
        '" . mysqli_real_escape_string($conecta, $qtd_funcionarios) . "', 
        '" . mysqli_real_escape_string($conecta, $descricao) . "'
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
