<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'atualizar' && !empty($id)) {

    $sql = "
    SELECT p.*,p.nr_proposta nr_documento,p.responsavel, p.responsavel_cpf, p.responsavel_email,
    e.id_empresa, e.razao_social, IF(e.tipo_inscricao = 1, 'CNPJ', 'CPF') tipo_inscricao_format, e.nr_inscricao, e.cidade, e.uf,
    CONCAT(LEFT(la.cnae,2), '.', MID(la.cnae,3,2), '-', RIGHT(la.cnae,1)) cnae_format, la.atividade, la.id_local_atividade, la.razao_social nome_local_atividade, la.atividade_principal, la.grau_risco grau_risco_local_atividade,
    sp.status_proposta,
    DATE_FORMAT(CURDATE(), '%d de %M de %Y') data
    FROM propostas p
    JOIN locais_atividade la ON (p.id_local_atividade = la.id_local_atividade)
    JOIN empresas e ON (la.id_empresa = e.id_empresa)
    JOIN status_propostas sp ON (sp.id_status_proposta = p.id_status_proposta)
    WHERE p.ativo = '1'
    AND p.id_proposta = $id
    ";

    // echo $sql; exit;
    $query  = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        $row = mysqli_fetch_object($query);
        $modelo_documento = file_get_contents('modelo_proposta.html');


        // FORMATAR NR DE INSCRIÇÃO DA EMPRESA
        if ($row->tipo_inscricao_format == 'CNPJ') {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
        } else {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '-' . substr($row->nr_inscricao, 9, 2);
        }

        // MONTAR TABELA COM SERVIÇOS INCLUSOS
        $sql0 = "
        SELECT rl.valor , rl.prazo , rl.observacoes ,
        s.servico
        FROM rl_propostas_servicos rl
        JOIN servicos s ON (rl.id_servico = s.id_servico)
        WHERE rl.id_proposta = $id
        AND rl.ativo = 1
        ORDER BY s.servico
        ";

        $query0 = mysqli_query($conecta, $sql0);

        if(mysqli_num_rows($query0) > 0) {
            while($row0 = mysqli_fetch_object($query0)) {

                if($row0->prazo == 0) {
                    $row0->prazo = "-";
                } elseif($row0->prazo == 1) {
                    $row0->prazo = "$row0->prazo mês";
                } else {
                    $row0->prazo = "$row0->prazo meses";
                }

                $row->tabela_servicos.= '
                <tr>
                    <td>'.$row0->servico.'</td>
                    <td>R$ '.number_format($row0->valor,2,',','.').'</td>
                    <td>'.$row0->prazo.'</td>
                    <td>'.$row0->observacoes.'</td>
                </tr>
                ';
            }
        } else {
            $row->tabela_servicos.= '
            <tr>
                <td colspan="4">Não há serviços vinculados a este documento.</td>
            </tr>
            ';
        }

        // CRIA ARRAY COM INFORMAÇÕES A SEREM SUBSTITUÍDAS
        $search = array(
            '{{atividade}}',
            '{{grau_risco_local_atividade}}',
            '{{cnae}}',
            '{{qtde_funcionarios}}',
            '{{qtde_funcoes}}',
            '{{atividade_principal}}',
            '{{nome_profissional}}',
            '{{especialidade}}',
            '{{orgao_profissional}}',
            '{{numero}}',
            '{{estado}}',
            '{{responsavel}}',
            '{{responsavel_cpf}}',
            '{{tabela_servicos}}',
            '{{razao_social}}',
            '{{data}}'
        );
        $replace = array(
            $row->atividade,
            str_pad($row->grau_risco_local_atividade,2,0,STR_PAD_LEFT),
            $row->cnae_format,
            str_pad($row->qtde_funcionarios,2,0,STR_PAD_LEFT),
            str_pad($row->qtde_funcoes,2,0,STR_PAD_LEFT),
            $row->atividade_principal,
            $row->nome_profissional,
            $row->especialidade,
            $row->orgao_profissional,
            $row->numero,
            $row->estado,
            $row->responsavel,
            $row->responsavel_cpf,
            $row->tabela_servicos,
            $row->razao_social,
            $row->data
        );

        $corpo_documento = str_replace($search, $replace, $modelo_documento);

        $sql = "
        UPDATE propostas SET
        corpo_documento = '" . mysqli_real_escape_string($conecta, $corpo_documento) . "'
        WHERE id_proposta = " . mysqli_real_escape_string($conecta, $id) . "
        ";

        // echo $sql; exit;
        $query  = mysqli_query($conecta, $sql);

        if ($query) {

            $result = json_encode(array(
                'success' => true,
                'result' => $corpo_documento,
            ));
        } else {

            $result = json_encode(array(
                'success' => false,
                'result' => 'Falha ao tentar salvar registro.'
            ));
        }

        echo $result;
    }
}
