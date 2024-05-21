<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// LISTAGEM DOS USUARIOS E PESQUISA PELO NOME E EMAIL
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $id = mysqli_real_escape_string($conecta, $id);
  $id_colaborador = mysqli_real_escape_string($conecta, trim($form["id_colaborador"]));
  $id_empresa = mysqli_real_escape_string($conecta, trim($form["id_empresa"]));
  $id_local_atividade = mysqli_real_escape_string($conecta, trim($form["id_local_atividade"]));
  $id_rl_setor_funcao = mysqli_real_escape_string($conecta, trim($form["id_rl_setor_funcao"]));
  $data_admissao = mysqli_real_escape_string($conecta, trim($form["data_admissao"]));
  $matricula = mysqli_real_escape_string($conecta, trim($form["matricula"]));
  $status = mysqli_real_escape_string($conecta, trim($form["status"]));

  if ($status == 1) {
    // VERIFICA SE JÁ NÃO HÁ VÍNCULO ATIVO DO COLABORADOR COM A EMPRESA
    $sql = "
    SELECT id_rl_colaborador_empresa
    FROM rl_colaboradores_empresas
    WHERE id_empresa = $id_empresa
    AND id_rl_colaborador_empresa <> $id
    AND id_colaborador = $id_colaborador
    AND status = 1
    ";
  }

  // echo $sql;exit;
  $query = mysqli_query($conecta, $sql);

  if (mysqli_num_rows($query) > 0) {
    $result = json_encode(array(
      'success' => false,
      'result' => 'Já existe um vínculo de trabalho ATIVO deste colaborador nesta empresa'
    ));
    echo $result;
    exit;
  }


  if ($id > 0) {


    $sql = "
    UPDATE rl_colaboradores_empresas SET
    id_rl_setor_funcao = '" . $id_rl_setor_funcao . "',
    data_admissao = '" . $data_admissao . "',
    matricula = '" . $matricula . "',
    status = '" . $status . "'
    WHERE id_rl_colaborador_empresa = " . $id;
  } else {

    $sql = "
    INSERT INTO rl_colaboradores_empresas (id_colaborador,id_empresa,id_local_atividade,id_rl_setor_funcao,data_admissao,matricula,status) VALUES 
      (
        '" . $id_colaborador . "', 
        '" . $id_empresa . "', 
        '" . $id_local_atividade . "', 
        '" . $id_rl_setor_funcao . "', 
        '" . $data_admissao . "', 
        '" . $matricula . "', 
        '" . $status . "'
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
