<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  // RECUPERANDO INFORMAÇÕES
  $renovacao = trim($form["renovacao"]);
  $qtde_funcionarios = trim($form["qtde_funcionarios"]);
  $qtde_funcoes = trim($form["qtde_funcoes"]);
  $id_status_proposta = trim($form["id_status_proposta"]);
  $id_empresa = trim($form["id_empresa"]);
  $id_local_atividade = trim($form["id_local_atividade"]);
  $responsavel = trim($form["responsavel"]);
  $responsavel_cpf = trim($form["responsavel_cpf"]);
  $responsavel_email = trim($form["responsavel_email"]);
  $consideracoes_finais = trim($form["consideracoes_finais"]);

  // SE TIVER ID - EDITAR CADASTRO EXISTENTE
  if ($id > 0) {

    $sql = "
      UPDATE propostas SET
      qtde_funcionarios = '" . mysqli_real_escape_string($conecta, $qtde_funcionarios) . "',
      qtde_funcoes = '" . mysqli_real_escape_string($conecta, $qtde_funcoes) . "',
      renovacao = '" . mysqli_real_escape_string($conecta, $renovacao) . "',
      responsavel = '" . mysqli_real_escape_string($conecta, $responsavel) . "',
      responsavel_cpf = '" . mysqli_real_escape_string($conecta, $responsavel_cpf) . "',
      responsavel_email = '" . mysqli_real_escape_string($conecta, $responsavel_email) . "',
      id_status_proposta = '" . mysqli_real_escape_string($conecta, $id_status_proposta) . "',
      consideracoes_finais = '" . mysqli_real_escape_string($conecta, $consideracoes_finais) . "'
      WHERE id_proposta = " . mysqli_real_escape_string($conecta, $id);
  }

  // SE ID VAZIO - INSERIR NOVO REGISTRO
  else {

    // Realizar a numeracao do documento
    $rs = mysqli_query($conecta, "select IFNULL(max(nr_proposta),0) as valor from propostas");
    if ($row = mysqli_fetch_object($rs)) {
      $max = $row->valor;
      if (($max - (intval(gmdate("y")) * 100000)) >= 0) {
        $nr_proposta = $max + 1;
      } else {
        $nr_proposta = intval(gmdate("y")) * 100000 + 1;
      }
    }

    $sql = "
      INSERT INTO propostas (data, renovacao, nr_proposta, id_empresa, id_local_atividade, qtde_funcionarios, qtde_funcoes, responsavel, responsavel_cpf, responsavel_email, consideracoes_finais) VALUES
      (
        CURDATE(),
        '" . mysqli_real_escape_string($conecta, $renovacao) . "',
        '" . mysqli_real_escape_string($conecta, $nr_proposta) . "',
        '" . mysqli_real_escape_string($conecta, $id_empresa) . "',
        '" . mysqli_real_escape_string($conecta, $id_local_atividade) . "',
        '" . mysqli_real_escape_string($conecta, $qtde_funcionarios) . "',
        '" . mysqli_real_escape_string($conecta, $qtde_funcoes) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel_cpf) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel_email) . "',
        '" . mysqli_real_escape_string($conecta, $consideracoes_finais) . "'
      )
      ";
    // echo $sql;exit;
    $query  = mysqli_query($conecta, $sql);
    $id = mysqli_insert_id($conecta);

    //  INCLUIR TODOS OS EXAMES PADRONIZADOS NA PROPOSTA DE TRABALHO
    $sql = "
      INSERT INTO rl_propostas_exames (id_proposta , id_exame , valor)
      SELECT $id , e.id_exame , e.valor_cobrar
      FROM exames e
      WHERE e.ativo = 1
      AND e.padronizar = 1
      ";
  }

  // echo $sql;exit;
  $query  = mysqli_query($conecta, $sql);

  if ($query) {
    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso.',
      'id' => $id,
      'data' => gmdate('Y-m-d')
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar editar registro'
    ));
  }

  echo $result;
}
