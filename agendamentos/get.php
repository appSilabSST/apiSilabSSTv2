<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_agendamento = trim($_GET["id"]);
            $sql = "
                SELECT a.*,ta.*,
				p.nr_pcmso,p.data_fim,p.data_fim,
                la.razao_social AS nome_local,la.nr_inscricao,e2.id_tipo_orgao as id_tipo_orgao_local,
				pf.nome AS nome_profissional,pf.orgao_nr,to_p.siglas as orgao_profissional,pf.orgao_uf,
                c.id_colaborador, c.id_tipo_orgao, c.nr_doc, c.nome nome_colaborador, c.nome_social,
                rl_ce.data_admissao,rl_ce.matricula,
                e.id_empresa, e.razao_social, 
                IF(rl_sf.funcao IS NULL, rl_ce.funcao, rl_sf.funcao) funcao,rl_sf.descricao,
                st.setor,st.id_setor,
                s.status_agendamento,
                (
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                    'id_exame', ex.id_exame,
                    'id_rl_agendamento_exame', rl_ae.id_rl_agendamento_exame,
                    'cod_esocial', ex.cod_esocial,
                    'procedimento', ex.procedimento,
                    'reaproveitado', rl_ae.reaproveitado,
                    'data', rl_ae.data,
                    'realizado', rl_ae.realizado,
                    'cobrar', rl_ae.cobrar,
                    'id_resultado_exame', rl_ae.id_resultado_exame,
                    'valor', rl_ae.valor,
                    'ativo', rl_ae.ativo
                ))
                FROM rl_agendamento_exames rl_ae
                JOIN exames ex ON (rl_ae.id_exame = ex.id_exame)
                WHERE rl_ae.id_agendamento = a.id_agendamento
                ) exames,
                (
                    SELECT JSON_ARRAYAGG(JSON_OBJECT(
                        'id_risco', r.id_risco,
                        'id_rl_agendamento_risco', rl_ar.id_rl_agendamento_risco,
                        'cod_esocial', r.cod_esocial,
                        'descricao', r.descricao,
                        'ativo', rl_ar.ativo
                ))
                FROM rl_agendamento_riscos rl_ar
                JOIN riscos r ON (rl_ar.id_risco = r.id_risco)
                WHERE rl_ar.id_agendamento = a.id_agendamento
                ) riscos
                FROM agendamentos a
                LEFT JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
                LEFT JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
                LEFT JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa)
                LEFT JOIN pcmso p ON (p.id_pcmso = a.id_pcmso)
                LEFT JOIN locais_atividade la ON (la.id_local_atividade = p.id_local_atividade)
                LEFT JOIN empresas e2 ON (e2.nr_doc = la.nr_inscricao)
                LEFT JOIN profissionais pf ON (pf.id_profissional = a.id_profissional)        
                LEFT JOIN tipos_orgao to_p ON (to_p.id_tipo_orgao = pf.id_tipo_orgao)        
                LEFT JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
                LEFT JOIN rl_setores_funcoes rl_sf ON (a.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
                LEFT JOIN setores st ON (st.id_setor = a.id_setor)
                LEFT JOIN status_agendamento s ON (a.id_status_agendamento = s.id_status_agendamento)
                WHERE a.ativo = '1' 
                AND a.id_agendamento = :id_agendamento
                ORDER BY a.horario, FIELD(a.nr_agendamento, NULL, a.nr_agendamento)
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
        }
        // SELECIONAR AFASTAMENTOS DE UMA EMPRESA ESPECÍFICA
        elseif (isset($_GET["data"]) && isset($_GET["id_sala"])) {
            $data = trim($_GET["data"]);
            $classe = trim($_GET["classe"]);
            $id_sala = trim($_GET["id_sala"]);

            if ($_GET["tipo"] == "externo") {
                $where = 'and a.id_local_atendimento > 1';
            } else {
                $where = 'and (a.id_local_atendimento = 1 OR a.id_local_atendimento = 0)';
            }

            $sql = "
            SELECT a.*,DATE_FORMAT(a.horario, '%H:%i') horario,
            s.status_agendamento,ta.tipo_atendimento,
            c.nome nome_colaborador,c.id_tipo_orgao,c.nr_doc,c.id_colaborador,c.sexo,c.data_nascimento,
            e.razao_social, e.id_empresa,
            IF(rl_sf.funcao IS NULL, rl_ce.funcao, rl_sf.funcao) funcao,
            COUNT(rl_ae.id_rl_agendamento_exame) AS countExames
            FROM agendamentos a
            JOIN rl_agendamento_exames rl_ae ON (rl_ae.id_agendamento = a.id_agendamento)
            JOIN rl_salas_exames  rl_se ON (rl_se.id_exame = rl_ae.id_exame)
            JOIN salas_atendimentos sl_at ON ( sl_at.id_sala_atendimento = rl_se.id_sala_atendimento)
            JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
            JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
         	JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa or a.id_empresa_reservado = e.id_empresa)
            LEFT JOIN rl_setores_funcoes rl_sf ON (a.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
            JOIN status_agendamento s ON (a.id_status_agendamento = s.id_status_agendamento)
            JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
            WHERE a.ativo = '1' 
            AND sl_at.id_sala_atendimento = :id_sala
            AND (a.id_status_agendamento = 2 OR a.id_status_agendamento = 2) 
            AND a.data = :data
            AND a.nr_agendamento IS NOT NULL
            $where
            ORDER BY horario,nome_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':data', $data);
        }
        // SELECIONAR AFASTAMENTOS DE UMA EMPRESA ESPECÍFICA
        elseif (isset($_GET["data"])) {
            $data = trim($_GET["data"]);

            if ($_GET["tipo"] == "externo") {
                $where = 'and a.id_local_atendimento > 1';
            } else {
                $where = 'and (a.id_local_atendimento = 1 OR a.id_local_atendimento = 0)';
            }

            $sql = "
            SELECT a.*,DATE_FORMAT(a.horario, '%H:%i') horario,
            s.status_agendamento,ta.tipo_atendimento,
            c.nome nome_colaborador,c.id_tipo_orgao,c.nr_doc,c.id_colaborador,c.sexo,c.data_nascimento,
            e.razao_social, e.id_empresa,e.nr_doc as nr_dor_empresa,e.id_tipo_orgao as id_tipo_orgao_empresa,
            IF(rl_sf.funcao IS NULL, rl_ce.funcao, rl_sf.funcao) funcao
            FROM agendamentos a
            LEFT JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
            LEFT JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
            LEFT JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa or a.id_empresa_reservado = e.id_empresa)
            LEFT JOIN rl_setores_funcoes rl_sf ON (a.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
            LEFT JOIN status_agendamento s ON (a.id_status_agendamento = s.id_status_agendamento)
            LEFT JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
            WHERE a.ativo = '1' 
            AND a.data = :data
            $where
            ORDER BY horario,nome_colaborador
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', $data);
        }
        // RETORNA MENSAGEM INFORMAÇÃO A OBRIGATORIEDADE EM ENVIAR UMA DATA
        else {
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
            echo json_encode($result);
            exit;
        }
        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            $result = http_response_code(204);
        } elseif (
            $stmt->rowCount() == 1 &&
            (
                isset($_GET["id"]) && is_numeric($_GET["id"]) ||
                isset($_GET["nr_doc"]) && is_numeric($_GET["nr_doc"])
            )
        ) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result['exames'] = json_decode($result['exames'], true);
            $result['riscos'] = json_decode($result['riscos'], true);
        } else {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // TRANSFORMA "EXAMES" E "RISCOS" EM OBJETO
            foreach ($result as $key => $value) {
                $result[$key]['exames'] = json_decode($value['exames'], true);
                $result[$key]['riscos'] = json_decode($value['riscos'], true);
            }
        }

        // $result = getResult($result);
    } catch (\Throwable $th) {
        http_response_code(502);
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(401);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
