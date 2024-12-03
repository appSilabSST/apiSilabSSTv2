<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_agendamento']) && isset($json['id_risco'])
        ) {
            $sql = "
            UPDATE rl_agendamentos_riscos SET
            id_agendamento = :id_agendamento,
            id_risco = :id_risco
            WHERE id_rl_agendamento_risco = :id_rl_agendamento_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', trim($json['id_agendamento']), PDO::PARAM_INT);
            $stmt->bindParam(':id_risco', trim($json['id_risco']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_agendamento_risco', trim($json['id']), PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Risco atualizado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao atualizar o risco!'
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
                'result' => 'Risco já existente neste agendamento!'
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
