<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);
$form = $postjson['form'];

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'salvar' && !empty($form)) {

  $id_status_documento = trim($form['id_status_documento']);
  $id_profissional = trim($form['id_profissional']);
  $id_empresa = trim($form['id_empresa']);
  $grau_risco_empresa = trim($form['grau_risco_empresa']);
  $id_local_atividade = trim($form['id_local_atividade']);
  $grau_risco_local_atividade = trim($form['grau_risco_local_atividade']);
  $responsavel = trim($form['responsavel']);
  $responsavel_cpf = trim($form['responsavel_cpf']);
  $responsavel_email = trim($form['responsavel_email']);
  $data_inicio = trim($form['data_inicio']) . "-01";
  $data_fim = trim($form['data_fim']) . "-01";
  $plano_emergencia = trim($form['plano_emergencia']);
  $consideracoes_finais = trim($form['consideracoes_finais']);
  $corpo_documento = trim($form['corpo_documento']);

  $error = array();

  // VERIFICA SE É PRA ATUALIZAR O CORPO DO DOCUMENTO
  if (!empty($corpo_documento)) {
    $sql = "
    UPDATE pgr SET
    corpo_documento = '" . mysqli_real_escape_string($conecta, $corpo_documento) . "'
    WHERE id_pgr = $id
    ";
    $query = mysqli_query($conecta, $sql);

    if ($query) {

      $result = json_encode(array(
        'success' => true,
        'result' => 'Registro salvo com sucesso.',
        'id' => $id
      ));
    } else {

      $result = json_encode(array(
        'success' => false,
        'result' => 'Falha ao tentar salvar registro.'
      ));
    }

    echo $result;
    exit;
  }

  // VERIFICA POSSIBILIDADES DE ERROS ANTES DE FINALIZAR O DOCUMENTO
  if ($id_status_documento == 2) {

    // VERIFICA SE HÁ REVISÃO SEM INFORMAÇÕES
    $sql = "
    SELECT r.id_revisao
    FROM revisoes r
    WHERE r.id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
    AND r.ativo = 1
    AND (
      r.revisao = '' OR
      r.descricao = ''
    )
    ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {
      $error[] = 'Existem REVISÕES que não há informações obrigatórias';
    }

    // VERIFICA SE HÁ SETOR SEM RISCO ALGUM, INCLUSIVE AUSÊNCIA DE RISCO CADASTRADO NOS GHE's
    $sql = "
    SELECT s.id_setor
    FROM setores s
    LEFT JOIN rl_setores_riscos rl ON s.id_setor = rl.id_setor 
    WHERE s.id_local_atividade = 
    (
      SELECT id_local_atividade
      FROM pgr
      WHERE id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
    )
    AND rl.id_rl_setor_risco IS NULL 
    AND s.ativo = 'S'
    LIMIT 1
    ";

    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {
      $error[] = 'Existem SETORES que não há informações de RISCOS';
    }

    // VERIFICA SE HÁ GHE's INDICANDO USO DE EPI, PORÉM NÃO HÁ EPI CADASTRADO
    $sql = "
    SELECT rl.id_rl_setor_risco
    FROM rl_setores_riscos rl
    LEFT JOIN rl_riscos_epis rl2 ON (rl.id_rl_setor_risco = rl2.id_rl_setor_risco)
    WHERE rl.epi_utiliza = 2
    AND rl2.id_rl_risco_epi IS NULL
    AND rl.id_setor IN
    (
      SELECT id_setor
      FROM setores
      WHERE id_local_atividade = 
      (
        SELECT id_local_atividade
        FROM pgr
        WHERE id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
      )
      AND ativo = 1
    )
    AND rl.ativo = 1
    ";
    // echo $sql;exit;
    $query = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {
      $error[] = 'Existem RISCOS indicando o uso de EPI que estão vazios';
    }
  }


  if (count($error) > 0) {
    echo json_encode(array(
      'success' => false,
      'result' => $error
    ));
    exit;
  }

  if ($id > 0) {

    // FECHA A REVISÃO CASO ESTEJA EM ABERTO
    $sql = "
    UPDATE revisoes SET
    status = 0
    WHERE id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
    AND status = 1
    AND ativo = 1
    ";

    mysqli_query($conecta, $sql);

    $sql = "
        UPDATE pgr SET
        id_status_documento = '" . mysqli_real_escape_string($conecta, $id_status_documento) . "',
        id_profissional = '" . mysqli_real_escape_string($conecta, $id_profissional) . "',
        grau_risco_empresa = '" . mysqli_real_escape_string($conecta, $grau_risco_empresa) . "',
        grau_risco_local_atividade = '" . mysqli_real_escape_string($conecta, $grau_risco_local_atividade) . "',
        data_inicio = '" . mysqli_real_escape_string($conecta, $data_inicio) . "',
        data_fim = '" . mysqli_real_escape_string($conecta, $data_fim) . "',
        responsavel = '" . mysqli_real_escape_string($conecta, $responsavel) . "',
        responsavel_cpf = '" . mysqli_real_escape_string($conecta, $responsavel_cpf) . "',
        responsavel_email = '" . mysqli_real_escape_string($conecta, $responsavel_email) . "',
        plano_emergencia = '" . mysqli_real_escape_string($conecta, $plano_emergencia) . "',
        consideracoes_finais = '" . mysqli_real_escape_string($conecta, $consideracoes_finais) . "'
        WHERE id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
        ";
  } else {

    // Realizar a numeracao do pgr
    $rs = mysqli_query($conecta, "select IFNULL(max(nr_pgr),0) as valor from pgr");
    if ($row = mysqli_fetch_object($rs)) {
      $max = $row->valor;
      if (($max - (intval(gmdate("y")) * 100000)) >= 0) {
        $nr_pgr = $max + 1;
      } else {
        $nr_pgr = intval(gmdate("y")) * 100000 + 1;
      }
    }

    $sql = "
      INSERT INTO pgr (nr_pgr, id_profissional, data_inicio, data_fim, id_empresa, id_local_atividade, grau_risco_empresa, grau_risco_local_atividade, responsavel, responsavel_cpf, responsavel_email, plano_emergencia, consideracoes_finais) VALUES
      (
        '" . mysqli_real_escape_string($conecta, $nr_pgr) . "',
        '" . mysqli_real_escape_string($conecta, $id_profissional) . "',
        '" . mysqli_real_escape_string($conecta, $data_inicio) . "',
        '" . mysqli_real_escape_string($conecta, $data_fim) . "',
        '" . mysqli_real_escape_string($conecta, $id_empresa) . "',
        '" . mysqli_real_escape_string($conecta, $id_local_atividade) . "',
        '" . mysqli_real_escape_string($conecta, $grau_risco_empresa) . "',
        '" . mysqli_real_escape_string($conecta, $grau_risco_local_atividade) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel_cpf) . "',
        '" . mysqli_real_escape_string($conecta, $responsavel_email) . "',
        '" . mysqli_real_escape_string($conecta, $plano_emergencia) . "',
        '" . mysqli_real_escape_string($conecta, $consideracoes_finais) . "'
      )
      ";
    $query = mysqli_query($conecta, $sql);
    $id_pgr = mysqli_insert_id($conecta);

    // INSERIR PLANOS DE AÇÃO PADRONIZADOS
    $sql = "
      INSERT INTO rl_setores_riscos_planos_acao (id_pgr , id_rl_setor_risco , plano_acao , descricao) 
      SELECT $id_pgr , rl_sr.id_rl_setor_risco ,
      pa.plano_acao , pa.descricao
      FROM rl_setores_riscos rl_sr
      JOIN setores s ON (rl_sr.id_setor = s.id_setor)
      JOIN planos_acao pa ON (pa.id_risco = rl_sr.id_risco AND pa.ativo = 1 AND pa.padronizar = 1)
      WHERE s.id_local_atividade = '$id_local_atividade'
      AND s.ativo = 1
      ";
  }

  // echo $sql; exit;
  $query  = mysqli_query($conecta, $sql);
  $id = mysqli_insert_id($conecta);

  if ($query) {

    $result = json_encode(array(
      'success' => true,
      'result' => 'Registro salvo com sucesso.',
      'id' => $id
    ));
  } else {

    $result = json_encode(array(
      'success' => false,
      'result' => 'Falha ao tentar salvar registro.'
    ));
  }

  echo $result;
}
