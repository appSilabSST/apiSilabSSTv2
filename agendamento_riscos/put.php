<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE agendamentos SET
            nr_agendamento = :nr_agendamento,
            data = :data,
            horario = :horario,
            id_pcmso = :id_pcmso,
            id_tipo_atendimento = :id_tipo_atendimento,
            id_rl_colaborador_empresa = :id_rl_colaborador_empresa,
            id_rl_setor_funcao = :id_rl_setor_funcao,
            id_profissional = :id_profissional,
            id_regra_agendamento = :id_regra_agendamento,
            encaixe = :encaixe,
            disponivel = :disponivel,
            cancelado = :cancelado
            WHERE id_agendamento = :id_agendamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nr_agendamento', trim($json['nr_agendamento']));
            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':horario', trim($json['horario']));
            $stmt->bindParam(':id_pcmso', trim($json['id_pcmso']), trim($json['id_pcmso']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_tipo_atendimento', trim($json['id_tipo_atendimento']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_colaborador_empresa', trim($json['id_rl_colaborador_empresa']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_setor_funcao', trim($json['id_rl_setor_funcao']), PDO::PARAM_INT);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
            $stmt->bindParam(':id_regra_agendamento', trim($json['id_regra_agendamento']), PDO::PARAM_INT);
            $stmt->bindParam(':encaixe', trim($json['encaixe']), PDO::PARAM_INT);
            $stmt->bindParam(':disponivel', trim($json['disponivel']));
            $stmt->bindParam(':cancelado', trim($json['cancelado']));
            $stmt->bindParam(':id_agendamento', trim($json['id']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Agendamento atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o agendamento!'
                );
            }
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Colaborador já existente nesta agenda!'
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => $th->getMessage()
            );
        }
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
