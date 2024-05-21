<?php

include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = trim($postjson['id']);

// SALVAR OU EDITAR EMPRESA
if ($postjson['requisicao'] == 'atualizar' && !empty($id)) {

    $sql = "
    SELECT p.id_pgr, p.nr_pgr, DATE_FORMAT(p.data_inicio, '%Y-%m') data_inicio, DATE_FORMAT(p.data_inicio, '%b/%y') data_inicio_format, DATE_FORMAT(p.data_fim, '%Y-%m') data_fim, DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, p.responsavel, p.responsavel_cpf, p.responsavel_email,p.grau_risco_empresa,p.grau_risco_local_atividade,p.plano_emergencia,p.id_profissional,p.consideracoes_finais,p.corpo_documento,
    e.id_empresa, e.razao_social, IF(e.tipo_inscricao = 1, 'CNPJ', 'CPF') tipo_inscricao_format, e.nr_inscricao, e.cidade, e.uf,
    l.id_local_atividade, l.razao_social nome_local_atividade, l.atividade_principal,
    s.id_status_documento, s.status_documento,
    pro.nome nome_profissional, pro.orgao_classe, pro.orgao_nr, pro.orgao_uf,
    es.nome especialidade,
    DATE_FORMAT(CURDATE(), '%d de %M de %Y') data
	FROM pgr p
    LEFT JOIN empresas e ON (p.id_empresa = e.id_empresa)
    LEFT JOIN locais_atividade l ON (p.id_local_atividade = l.id_local_atividade)
    LEFT JOIN status_documentos s ON (s.id_status_documento = p.id_status_documento)
    LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
    LEFT JOIN especialidades es ON (es.id_especialidade = pro.id_especialidade)
    WHERE p.ativo = 1
    AND id_pgr = $id
    ";

    // echo $sql; exit;
    $query  = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        $row = mysqli_fetch_object($query);
        $modelo_documento = file_get_contents('modelo_pgr.html');


        // FORMATAR NR DE INSCRIÇÃO DA EMPRESA
        if ($row->tipo_inscricao_format == 'CNPJ') {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
        } else {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '-' . substr($row->nr_inscricao, 9, 2);
        }

        // MONTAR TABELA DE PLANOS DE AÇÃO
        $sql0 = "
        SELECT plano_acao, descricao, DATE_FORMAT(data_avaliacao, '%d/%m/%Y') data_avaliacao_format, DATE_FORMAT(data_conclusao, '%d/%m/%Y') data_conclusao_format
        FROM rl_setores_riscos_planos_acao
        WHERE id_pgr = $id
        AND ativo = 1
        ORDER BY data_avaliacao
        ";

        $query0 = mysqli_query($conecta, $sql0);
        $row->plano_acao = '';

        if (mysqli_num_rows($query0) > 0) {
            $cont = 0;
            $row->plano_acao = '
            <figure class="table">
                <table>
                    <thead>
                        <tr>
                            <th><p style="text-align:center;"><strong>ITEM</strong></p></th>
                            <th><p style="text-align:justify;"><strong>MEDIDA DE CONTROLE</strong></p></th>
                            <th><p style="text-align:justify;"><strong>DESCRIÇÃO</strong></p></th>
                            <th><p style="text-align:center;"><strong>PREVISÃO DE<br>EXECUÇÃO</strong></p></th>
                            <th><p style="text-align:center;"><strong>DATA DE<br>CONCLUSÃO</strong></p></th>
                        </tr>
                    </thead>
                    <tbody>
            ';
            while ($row0 = mysqli_fetch_object($query0)) {
                $row->plano_acao .= '
                <tr>
                    <td><p style="text-align:center;">' . ++$cont . '</p></td>
                    <td><p style="text-align:justify;">' . $row0->plano_acao . '</p></td>
                    <td><p style="text-align:justify;">' . $row0->descricao . '</p></td>
                    <td><p style="text-align:center;">' . $row0->data_avaliacao_format . '</p></td>
                    <td><p style="text-align:center;"> </p></td>
                </tr>
                ';
            }
            $row->plano_acao .= '
            </tbody>
            </table>
            </figure>
            <span class="text-tiny">
                [1] A revisão do PGR deverá ocorrer no máximo a cada dois anos ou quando da ocorrência de uma ou mais das seguintes condições:
                <ul style="list-style-type:circle;">
                    <li>após implementação das medidas de prevenção, para avaliação de riscos residuais;</li>
                    <li>após inovações e modificações nas tecnologias, ambientes, processos, condições, procedimentos e organização do trabalho que impliquem em novos riscos ou modifiquem os riscos existentes;</li>
                    <li>quando identificadas inadequações, insuficiências ou ineficácias das medidas de prevenção;</li>
                    <li>na ocorrência de acidentes ou doenças relacionadas ao trabalho;</li>
                    <li>quando houver mudança nos requisitos legais aplicáveis.</li>
                </ul>
            </span>
            ';
        }

        // MONTAR TABELA DE CONTROLE DE REVISÕES
        $sql1 = "
        SELECT DATE_FORMAT(data, '%b/%y') data_format, revisao, descricao, status, IF(status = 0, 'FECHADA', 'ABERTA') status_format
        FROM revisoes
        WHERE id_pgr = $id
        AND ativo = 1
        ORDER BY data DESC
        ";

        $query1 = mysqli_query($conecta, $sql1);
        $row->controle_revisoes = '';

        if (mysqli_num_rows($query1) > 0) {
            $cont = 0;
            $row->controle_revisoes = '
            <figure class="table">
                <table>
                    <thead>
                        <tr>
                            <th><p style="text-align:center;"><strong>Nº REV</strong></p></th>
                            <th><p style="text-align:center;"><strong>DATA</strong></p></th>
                            <th><p style="text-align:justify;"><strong>REVISÃO</strong></p></th>
                            <th><p style="text-align:justify;"><strong>DESCRIÇÃO</strong></p></th>
                            <th><p style="text-align:center;"><strong>STATUS</strong></p></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><p style="text-align:center;">' . $cont++ . '</p></td>
                        <td><p style="text-align:justify;">' . $row->data_inicio_format . '</p></td>
                        <td><p style="text-align:justify;"> Emissão original </p></td>
                        <td><p style="text-align:justify;"> Primeira via emitida </p></td>
                        <td><p style="text-align:center;">' . $row->status_documento . '</p></td>
                    </tr>
            ';
            while ($row1 = mysqli_fetch_object($query1)) {
                $row->controle_revisoes .= '
                <tr>
                    <td><p style="text-align:center;">' . $cont++ . '</p></td>
                    <td><p style="text-align:justify;">' . $row1->data_format . '</p></td>
                    <td><p style="text-align:justify;">' . $row1->revisao . '</p></td>
                    <td><p style="text-align:justify;">' . $row1->descricao . '</p></td>
                    <td><p style="text-align:center;">' . $row1->status_format . '</p></td>
                </tr>
                ';
            }
            $row->controle_revisoes .= '
            </tbody>
            </table>
            </figure>
            ';
        }

        // MONTAR TABELA COM LEVANTAMENTO DE RISCOS
        $row->levantamento_riscos = '';

        // LISTAR SETORES CADASTROS NO GHE
        $sql1 = "
        SELECT s.id_setor, s.setor, s.descricao, IF(s.status = 0, 'Inativo', 'Ativo') status_format
        FROM setores s
        WHERE s.id_local_atividade = $row->id_local_atividade
        AND s.ativo = 1
        ORDER BY s.setor
        ";

        // echo $sql1;exit;
        $query1 = mysqli_query($conecta, $sql1);
        if (mysqli_num_rows($query1) > 0) {

            while ($row1 = mysqli_fetch_object($query1)) {
                $row->levantamento_riscos .= '
                <figure class="table">
                    <table>
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>PROGRAMA DE GERENCIAMENTO DE RISCOS | INVENTÁRIO DE RISCOS </strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>'.$row->data_inicio_format.'</strong></span></p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                ';

                $row->levantamento_riscos .= '
                <figure class="table">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="text-tiny"><strong>EMPRESA</strong></span><br>
                                    <span class="text-small">' . $row->razao_social . '</span>
                                </td>
                                <td>
                                    <span class="text-tiny"><strong>' . $row->tipo_inscricao_format . '</strong></span><br>
                                    <span class="text-tiny">' . $row->nr_inscricao_format . '</span>
                                </td>
                                <td>
                                    <span class="text-tiny"><strong>CIDADE / UF</strong></span><br>
                                    <span class="text-tiny">' . $row->cidade . ' / ' . $row->uf . '</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="text-tiny"><strong>GHE - DEPARTAMENTO (STATUS)</strong></span><br>
                                    <span class="text-small">' . $row1->setor . ' (' . $row1->status_format . ')</span>
                                </td>
                                <td colspan="2">
                                    <span class="text-tiny"><strong>DESCRIÇÃO</strong></span><br>
                                    <span class="text-small">' . $row1->descricao . '</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </figure>
                ';

                // LISTAR FUNÇÕES CADASTRADAS NO GHE
                $sql2 = "
                SELECT funcao, descricao, qtd_funcionarios, jornada_trabalho
                FROM rl_setores_funcoes
                WHERE id_setor = $row1->id_setor
                AND ativo = 1
                ";

                $query2 = mysqli_query($conecta, $sql2);

                if (mysqli_num_rows($query2) > 0) {
                    $row->levantamento_riscos .= '
                    <figure class="table">
                    <table>
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>FUNÇÃO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>QTDE</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>JORNADA DE TRABALHO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>DESCRIÇÃO</strong></span></p></th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    while ($row2 = mysqli_fetch_object($query2)) {
                        $row->levantamento_riscos .= '
                        <tr>
                            <td><p style="text-align:left;"><span class="text-tiny">' . $row2->funcao . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row2->qtd_funcionarios . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row2->jornada_trabalho . '</span></p></td>
                            <td><p style="text-align:justify;"><span class="text-tiny">' . $row2->descricao . '</span></p></td>
                        </tr>
                        ';
                    }

                    $row->levantamento_riscos .= '
                    </tbody>
                    </table>
                    </figure>
                    ';
                }

                // LISTAR AGENTES DE RISCOS CADASTRADOS NO GHE
                $sql3 = "
                SELECT rl.limite_tolerancia, IF(rl.tipo_avaliacao = 1, 'N/A', CONCAT_WS(' ', rl.intensidade, um.sigla)) intensidade_format, rl.fonte_geradora, rl.medidas_controle, rl.probabilidade, rl.severidade,
                r.descricao, r.grupo, r.danos_saude,
                te.tipo_exposicao, te.tempo_exposicao,
                mp.meio_propagacao,
                cr.classificacao_risco , cr.descricao descricao_classificacao_risco
                FROM rl_setores_riscos rl
                JOIN riscos r ON (r.id_risco = rl.id_risco)
                JOIN tipos_exposicao te ON (te.id_tipo_exposicao = rl.id_tipo_exposicao)
                JOIN meios_propagacao mp ON (mp.id_meio_propagacao = rl.id_meio_propagacao)
                JOIN classificacao_riscos cr ON (cr.id_classificacao_risco = rl.id_classificacao_risco)
                LEFT JOIN unidades_medida um ON (um.id_unidade_medida = rl.id_unidade_medida)
                WHERE rl.id_setor = $row1->id_setor
                AND rl.ativo = 1
                ORDER BY FIELD(LEFT(r.grupo, 1), 'F', 'Q', 'B', 'E', 'A'), r.descricao
                ";

                $query3 = mysqli_query($conecta, $sql3);

                if (mysqli_num_rows($query3) > 0) {
                    $row->levantamento_riscos .= '
                    <figure class="table">
                    <table>
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>TIPO RISCO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>AGENTE NOCIVO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>LIMITE TOLERÂNCIA</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>INTENSIDADE / CONCENTRAÇÃO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>TIPO EXPOSIÇÃO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>FONTE GERADORA</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>MEIO EXPOSIÇÃO / FORMA PROPAGAÇÃO</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>DANOS A SAÚDE</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>MEDIDAS DE CONTROLE</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>S</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>P</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>R</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>CLASSIFICAÇÃO DO RISCO</strong></span></p></th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    while ($row3 = mysqli_fetch_object($query3)) {
                        $row->levantamento_riscos .= '
                        <tr>
                            <td><p style="text-align:center;"><span class="text-tiny">' . substr($row3->grupo, 0, 1) . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->descricao . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->limite_tolerancia . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->intensidade_format . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->tipo_exposicao . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->fonte_geradora . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->meio_exposicao . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->danos_saude . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->medidas_controle . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->severidade . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . $row3->probabilidade . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny">' . ($row3->severidade * $row3->probabilidade) . '</span></p></td>
                            <td><p style="text-align:center;"><span class="text-tiny"><strong>' . $row3->classificacao_risco . '</strong> - ' . $row3->descricao_classificacao_risco . '</span></p></td>
                        </tr>
                        ';
                    }

                    $row->levantamento_riscos .= '
                    </tbody>
                    </table>
                    </figure>
                    ';
                }

                $row->levantamento_riscos .= '
                </td>
                </tr>
                </tbody>
                </table>
                </figure>
                
                <figure class="table">
                    <table>
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>PROFISSIONAL RESPONSÁVEL</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>LEGENDA</strong></span></p></th>
                                <th><p style="text-align:center;"><span class="text-tiny"><strong>NOTA</strong></span></p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="text-tiny">' . $row->nome_profissional . '</span><br>
                                    <span class="text-tiny">' . $row->especialidade . '</span><br>
                                    <span class="text-tiny">' . $row->orgao_classe . ' ' . $row->orgao_nr . '/' . $row->orgao_uf . '</span><br>
                                </td>
                                <td>
                                    <span class="text-tiny">(N/A) Não aplicável</span><br>
                                    <span class="text-tiny"><strong>Tipo de risco:</strong></span><br>
                                    <span class="text-tiny">(F) Físico (Q) Químico (B) Biológico (E) Ergonômico (A) Acidente</span><br>
                                    <span class="text-tiny"><strong>(S) Severidade:</strong></span><br>
                                    <span class="text-tiny">(0) Nenhuma (1) Leve (2) Significativa (3) Severa (4) Catastrófica</span><br>
                                    <span class="text-tiny"><strong>(P) Probabilidade:</strong></span><br>
                                    <span class="text-tiny">(1) Improvável (2) Rara (3) Remota (4) Possível (5) Provável (6) Certa</span><br>
                                    <span class="text-tiny"><strong>(R) Risco:</strong></span><br>
                                    <span class="text-tiny">(0) Insignificante (1 a 5) Aceitável (6 a 9) Moderado (10 a 15) Alto (16 a 24) Inaceitável</span>
                                </td>
                                <td>
                                    <span class="text-tiny">
                                        1. Há a possibilidade de riscos ergonômicos relacionados às atividades exercidas pelas funções constantes no GHE. Para melhor entendimento, é necessária elaboração de Avaliação Ergonômica Preliminar - AEP.
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </figure>
                ';

                if($cont++ < mysqli_num_rows($query1)) {
                    $row->levantamento_riscos .= '<div style="page-break-after: always;"></div>';
                }
            }
        }

        // CRIA ARRAY COM INFORMAÇÕES A SEREM SUBSTITUÍDAS
        $search = array(
            '{{atividade_principal}}',
            '{{plano_acao}}',
            '{{nome_profissional}}',
            '{{especialidade}}',
            '{{orgao_classe}}',
            '{{orgao_nr}}',
            '{{orgao_uf}}',
            '{{responsavel}}',
            '{{responsavel_cpf}}',
            '{{levantamento_riscos}}',
            '{{razao_social}}',
            '{{controle_revisoes}}',
            '{{data}}'
        );
        $replace = array(
            $row->atividade_principal,
            $row->plano_acao,
            $row->nome_profissional,
            $row->especialidade,
            $row->orgao_classe,
            $row->orgao_nr,
            $row->orgao_uf,
            $row->responsavel,
            $row->responsavel_cpf,
            $row->levantamento_riscos,
            $row->razao_social,
            $row->controle_revisoes,
            $row->data
        );

        $corpo_documento = str_replace($search, $replace, $modelo_documento);

        $sql = "
        UPDATE pgr SET
        corpo_documento = '" . mysqli_real_escape_string($conecta, $corpo_documento) . "'
        WHERE id_pgr = " . mysqli_real_escape_string($conecta, $id) . "
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
