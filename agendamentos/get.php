<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        // SELECIONAR UM AGENDAMENTO ESPECÍFICO
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_agendamento = trim($_GET["id"]);
            $sql = "
            SELECT a.id_agendamento, a.id_rl_colaborador_empresa, a.id_rl_setor_funcao, a.nr_agendamento, a.id_pcmso, a.data, DATE_FORMAT(a.data, '%d/%m/%Y') data_format, a.horario, DATE_FORMAT(a.horario, '%H:%i') horario_format, a.cancelado,a.encaixe,a.justificativa_remarcacao,a.observacao,
            c.id_colaborador, c.tipo_doc, c.nr_doc, c.nome nome_colaborador, c.nome_social,
            e.id_empresa, e.razao_social, 
            ta.id_tipo_atendimento, ta.tipo_atendimento,
            rl_sf.id_setor, IF(rl_sf.funcao IS NULL, a.funcao, rl_sf.funcao) funcao,
            s.status_agendamento
            FROM agendamentos a
            LEFT JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
            LEFT JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
            LEFT JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa)
            LEFT JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
            LEFT JOIN rl_setores_funcoes rl_sf ON (a.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
            LEFT JOIN status_agendamento s ON (a.id_status_agendamento = s.id_status_agendamento)
            WHERE a.ativo = '1' 
            AND a.id_agendamento = :id_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', $id_agendamento);
        }
        // SELECIONAR AFASTAMENTOS DE UMA EMPRESA ESPECÍFICA
        elseif (isset($_GET["data"])) {
            $data = trim($_GET["data"]);

            if (isset($_GET["externo"])) {
                $where = 'and a.id_local_atendimento > 1';
            } else {
                $where = 'and (a.id_local_atendimento = 1 OR a.id_local_atendimento = 0)';
            }

            $sql = "
            SELECT a.id_agendamento, a.id_rl_colaborador_empresa, a.id_rl_setor_funcao, a.nr_agendamento, a.id_pcmso, a.data, DATE_FORMAT(a.data, '%d/%m/%Y') data_format, a.horario, DATE_FORMAT(a.horario, '%H:%i') horario_format, a.cancelado,a.encaixe,a.justificativa_remarcacao,a.observacao,
            c.id_colaborador, c.tipo_doc, c.nr_doc, c.nome nome_colaborador, c.nome_social,
            e.id_empresa, e.razao_social, 
            ta.id_tipo_atendimento, ta.tipo_atendimento,
            rl_sf.id_setor, IF(rl_sf.funcao IS NULL, a.funcao, rl_sf.funcao) funcao,
            s.status_agendamento
            FROM agendamentos a
            LEFT JOIN rl_colaboradores_empresas rl_ce ON (a.id_rl_colaborador_empresa = rl_ce.id_rl_colaborador_empresa)
            LEFT JOIN colaboradores c ON (rl_ce.id_colaborador = c.id_colaborador)
            LEFT JOIN empresas e ON (rl_ce.id_empresa = e.id_empresa)
            LEFT JOIN tipos_atendimento ta ON (a.id_tipo_atendimento = ta.id_tipo_atendimento)
            LEFT JOIN rl_setores_funcoes rl_sf ON (a.id_rl_setor_funcao = rl_sf.id_rl_setor_funcao)
            LEFT JOIN status_agendamento s ON (a.id_status_agendamento = s.id_status_agendamento)
            WHERE a.ativo = '1' 
            AND a.data = :data
            $where
            ORDER BY a.horario, FIELD(a.nr_agendamento, NULL, a.nr_agendamento)
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
            $result = array(
                'status' => 'fail',
                'result' => 'Nenhum agendamento foi encontrado'
            );
        } elseif ($stmt->rowCount() == 1 && isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $dados = $stmt->fetch(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        } else {
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result = array(
                'status' => 'success',
                'result' => $dados
            );
        }
    } catch (\Throwable $th) {
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
} else {
    http_response_code(403);
    echo json_encode(
        array(
            'status' => 'fail',
            'result' => 'Sem autorização para acessar este conteúdo!'
        )
    );
}
exit;
