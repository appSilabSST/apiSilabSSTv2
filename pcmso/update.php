<?php

include_once('../conexao.php');

$id = trim($_GET['id']);

// SALVAR OU EDITAR EMPRESA
if (!empty($id)) {


    $sql = "
    SELECT p.id_pcmso, p.nr_pcmso, p.nr_pcmso nr_controle, DATE_FORMAT(p.data_inicio, '%Y-%m') data_inicio,
    DATE_FORMAT(p.data_inicio, '%b/%y') data_inicio_format, DATE_FORMAT(p.data_fim, '%Y-%m') data_fim,
    DATE_FORMAT(p.data_fim, '%b/%y') data_fim_format, p.responsavel, p.responsavel_cpf,
    p.responsavel_email,p.grau_risco,p.id_profissional,p.consideracoes_finais,p.corpo_documento,
    e.id_empresa, e.razao_social, IF(e.id_tipo_orgao = 2, 'CNPJ', 'CPF') tipo_inscricao_format, e.cidade,
    e.uf,
    l.id_local_atividade, l.razao_social nome_local_atividade, l.atividade_principal,
    s.id_status_documento, s.status_documento,
    pro.nome nome_profissional, esp.siglas  as orgao_profissional, pro.numero, pro.estado,
    es.nome especialidade,
    DATE_FORMAT(CURDATE(), '%d de %M de %Y') data
    FROM pcmso p
    LEFT JOIN empresas e ON (p.id_empresa = e.id_empresa)
    LEFT JOIN locais_atividade l ON (p.id_local_atividade = l.id_local_atividade)
    LEFT JOIN status_documentos s ON (s.id_status_documento = p.id_status_documento)
    LEFT JOIN profissionais pro ON (p.id_profissional = pro.id_profissional)
    LEFT JOIN especialidades es ON (es.id_especialidade = pro.id_especialidade)
    LEFT JOIN especialidades esp ON (esp.id_especialidade = pro.id_especialidade)  
    WHERE p.ativo = 1
    AND id_pcmso = $id
    ";

    // echo $sql; exit;
    $query  = mysqli_query($conecta, $sql);
    if (mysqli_num_rows($query) > 0) {

        $row = mysqli_fetch_object($query);
        $modelo_documento = file_get_contents('modelo_pcmso.html');


        // FORMATAR NR DE INSCRIÇÃO DA EMPRESA
        if ($row->tipo_inscricao_format == 'CNPJ') {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 2) . '.' . substr($row->nr_inscricao, 2, 3) . '.' . substr($row->nr_inscricao, 5, 3) . '/' . substr($row->nr_inscricao, 8, 4) . '-' . substr($row->nr_inscricao, 12, 2);
        } else {
            $row->nr_inscricao_format = substr($row->nr_inscricao, 0, 3) . '.' . substr($row->nr_inscricao, 3, 3) . '.' . substr($row->nr_inscricao, 6, 3) . '-' . substr($row->nr_inscricao, 9, 2);
        }

        // MONTAR TABELA DE CONTROLE DE REVISÕES
        $sql1 = "
                SELECT DATE_FORMAT(revisoes.data_inicio, '%b/%y') data_format, revisao, descricao, status, IF(status = 0, 'FECHADA', 'ABERTA') status_format
                FROM revisoes
                WHERE id_pcmso = $id
                AND ativo = 1
                ORDER BY revisoes.data_inicio DESC
            ";

        $query1 = mysqli_query($conecta, $sql1);

        $row->controle_revisoes = '
            
            <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
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
                    <td><p style="text-align:center;"> 1 </p></td>
                    <td><p style="text-align:justify;">' . $row->data_inicio_format . '</p></td>
                    <td><p style="text-align:justify;"> Emissão original </p></td>
                    <td><p style="text-align:justify;"> Primeira via emitida </p></td>
                    <td><p style="text-align:center;">' . $row->status_documento . '</p></td>
                </tr>
            ';

        if (mysqli_num_rows($query1) > 0) {
            $cont = 1;
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
        }
        $row->controle_revisoes .= '
            </tbody>
            </table>
        ';

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
                    <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
                        <caption>
                            <span style="font-size: 6pt"><strong>10.1 - TABELAS DE POSSÍVEIS AGRAVOS À SAÚDE RELACIONADOS AOS RISCOS OCUPACIONAIS IDENTIFICADOS E CLASSIFICADOS NO PGR, CONFORME ALÍNEA a) DO ÍTEM 7.5.4.</strong></span>
                        </caption>
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><span style="font-size: 8pt"><strong> INVENTÁRIO DE RISCOS </strong></span></p></th>
                                <th><p style="text-align:center;"><span style="font-size: 8pt"><strong>' . $row->data_inicio_format . '</strong></span></p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                ';

                $row->levantamento_riscos .= '
                
                    <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
                        <tbody>
                            <tr>
                                <td>
                                    <span style="font-size: 7pt"><strong>GHE - DEPARTAMENTO (STATUS)</strong></span><br>
                                    <span style="font-size: 8pt">' . $row1->setor . ' (' . $row1->status_format . ')</span>
                                </td>
                                <td>
                                    <span style="font-size: 7pt"><strong>DESCRIÇÃO</strong></span><br>
                                    <span style="font-size: 8pt">' . $row1->descricao . '</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                
                ';

                // LISTAR FUNÇÕES CADASTRADAS NO GHE
                $sql2 = "
                SELECT GROUP_CONCAT(funcao SEPARATOR ' | ') lista_funcoes
                FROM rl_setores_funcoes
                WHERE id_setor = $row1->id_setor
                AND ativo = 1
                ";

                $query2 = mysqli_query($conecta, $sql2);

                if (mysqli_num_rows($query2) > 0) {
                    $row->levantamento_riscos .= '
                    
                    <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
                        <tbody>
                    ';
                    while ($row2 = mysqli_fetch_object($query2)) {
                        $row->levantamento_riscos .= '
                        <tr>
                            <td>
                                <span style="font-size: 7pt"><strong>FUNÇÕES</strong></span><br>
                                <span style="font-size: 8pt">' . $row2->lista_funcoes . '</span>
                            </td>
                        </tr>
                        ';
                    }

                    $row->levantamento_riscos .= '
                    </tbody>
                    </table>
                    
                    ';
                }

                // LISTAR AGENTES DE RISCOS CADASTRADOS NO GHE
                $sql3 = "
                SELECT r.descricao, r.grupo, r.danos_saude
                FROM rl_setores_riscos rl
                JOIN riscos r ON (r.id_risco = rl.id_risco)
                WHERE rl.id_setor = $row1->id_setor
                AND rl.ativo = 1
                ORDER BY FIELD(LEFT(r.grupo, 1), 'F', 'Q', 'B', 'E', 'A'), r.descricao
                ";

                $query3 = mysqli_query($conecta, $sql3);

                if (mysqli_num_rows($query3) > 0) {
                    $row->levantamento_riscos .= '
                    
                    <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
                        <thead>
                            <tr>
                                <th><p style="text-align:center;"><strong>TIPO RISCO</strong></p></th>
                                <th><p style="text-align:justify;"><strong>AGENTE NOCIVO</strong></p></th>
                                <th><p style="text-align:justify;"><strong>POSSÍVEIS DANOS À SAÚDE</strong></p></th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    while ($row3 = mysqli_fetch_object($query3)) {
                        $row->levantamento_riscos .= '
                        <tr>
                            <td><p style="text-align:center;">' . $row3->grupo . '</p></td>
                            <td><p style="text-align:justify;">' . $row3->descricao . '</p></td>
                            <td><p style="text-align:justify;">' . $row3->danos_saude . '</p></td>
                        </tr>
                        ';
                    }

                    $row->levantamento_riscos .= '
                    </tbody>
                    </table>
                    
                    ';

                    $sql4 = "
                    SELECT IF(rl.admissional = 1, 'X', '-') admissional_x , IF(rl.demissional = 1, 'X', '-') demissional_x , IF(rl.periodico = 1, 'X', '-') periodico_x , IF(rl.mudanca_risco = 1, 'X', '-') mudanca_risco_x , IF(rl.retorno_trabalho = 1, 'X', '-') retorno_trabalho_x , CONCAT(rl.periodicidade, ' MESES') periodicidade_format ,
                    e.procedimento
                    FROM rl_setores_exames rl
                    JOIN exames e ON (rl.id_exame = e.id_exame)
                    WHERE rl.id_pcmso = $id
                    AND rl.id_setor = $row1->id_setor
                    AND rl.ativo = 1
                    ";

                    $query4 = mysqli_query($conecta, $sql4);

                    if (mysqli_num_rows($query4) > 0) {
                        $row->levantamento_riscos .= '
                        <table style="border-collapse: collapse; width: 100%; border-width: 1px; font-size: 8pt;" border="1">
                            <caption>
                                <span style="font-size: 6pt"><strong>10.2 - TABELAS DE PLANEJAMENTO DE EXAMES OCUPACIONAIS CLÍNICOS E COMPLEMENTARES RELACIONADOS AOS RISCOS OCUPACIONAIS IDENTIFICADOS E CLASSIFICADOS NO PGR, POR LOCAL E/OU FUNÇÃO E/OU GHE, CONFORME ALÍNEA b) DO ÍTEM 7.5.4.</strong></span>
                            </caption>
                            <thead>
                                <tr>
                                    <th><p style="text-align:justify;"><strong>EXAMES OCUPACIONAIS</strong></p></th>
                                    <th><p style="text-align:center;"><strong>ADMISSIONAL</strong></p></th>
                                    <th><p style="text-align:center;"><strong>PERIÓDICO</strong></p></th>
                                    <th><p style="text-align:center;"><strong>MUDANÇA DE RISCOS<br>OCUPACIONAIS</strong></p></th>
                                    <th><p style="text-align:center;"><strong>RETORNO AO TRABALHO</strong></p></th>
                                    <th><p style="text-align:center;"><strong>DEMISSIONAL</strong></p></th>
                                </tr>
                            </thead>
                            <tbody>
                        ';
                        while ($row4 = mysqli_fetch_object($query4)) {
                            $row->levantamento_riscos .= '
                            <tr>
                                <td><p style="text-align:justify;">' . $row4->procedimento . '</p></td>
                                <td><p style="text-align:center;">' . $row4->admissional_x . '</p></td>
                                <td><p style="text-align:center;">' . $row4->periodico_x . '<br>(' . $row4->periodicidade_format . ')</p></td>
                                <td><p style="text-align:center;">' . $row4->mudanca_risco_x . '</p></td>
                                <td><p style="text-align:center;">' . $row4->retorno_trabalho_x . '</p></td>
                                <td><p style="text-align:center;">' . $row4->demissional_x . '</p></td>
                            </tr>
                            ';
                        }
                        $row->levantamento_riscos .= '
                        </tbody>
                        </table>
                        
                        ';
                    }
                }

                $row->levantamento_riscos .= '
                </td>
                </tr>
                </tbody>
                </table>
                ';

                if ($cont++ < mysqli_num_rows($query1)) {
                    $row->levantamento_riscos .= '<p><!-- pagebreak --></p>';
                }
            }
        }

        // CRIA ARRAY COM INFORMAÇÕES A SEREM SUBSTITUÍDAS
        $search = array(
            '{{data_inicio_format}}',
            '{{data_fim_format}}',
            '{{nr_controle}}',
            '{{atividade_principal}}',
            '{{nome_profissional}}',
            '{{especialidade}}',
            '{{orgao_profissional}}',
            '{{numero}}',
            '{{estado}}',
            '{{responsavel}}',
            '{{responsavel_cpf}}',
            '{{levantamento_riscos}}',
            '{{razao_social}}',
            '{{controle_revisoes}}',
            '{{data}}'
        );
        $replace = array(
            $row->data_inicio_format,
            $row->data_fim_format,
            $row->nr_controle,
            $row->atividade_principal,
            $row->nome_profissional,
            $row->especialidade,
            $row->orgao_profissional,
            $row->numero,
            $row->estado,
            $row->responsavel,
            $row->responsavel_cpf,
            $row->levantamento_riscos,
            $row->razao_social,
            $row->controle_revisoes,
            $row->data
        );

        $corpo_documento = str_replace($search, $replace, $modelo_documento);

        // $sql = "
        // UPDATE pcmso SET
        // corpo_documento = '" . mysqli_real_escape_string($conecta, $corpo_documento) . "'
        // WHERE id_pcmso = " . mysqli_real_escape_string($conecta, $id) . "
        // ";

        // // echo $sql; exit;
        // $query  = mysqli_query($conecta, $sql);

        // if ($query) {

        $result = json_encode(array(
            'success' => true,
            'corpo_modelo' => $corpo_documento,
        ));
        // } else {

        //     $result = json_encode(array(
        //         'success' => false,
        //         'result' => 'Falha ao tentar salvar registro.'
        //     ));
        // }

        echo $result;
    }
}
