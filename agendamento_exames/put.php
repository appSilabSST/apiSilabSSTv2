<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE rl_agendamento_exames SET
            id_agendamento = :id_agendamento,
            id_exame = :id_exame,
            data = :data,
            realizado = :realizado,
            id_reaproveitado = :id_reaproveitado,
            pago = :pago,
            id_resultado_exame = :id_resultado_exame,
            ativo = :ativo
            WHERE id_rl_agendamento_exame = :id_rl_agendamento_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_agendamento', trim($json['id_agendamento']));
            $stmt->bindParam(':id_exame', trim($json['id_exame']), PDO::PARAM_INT);
            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':realizado', trim($json['realizado']), PDO::PARAM_INT);
            $stmt->bindParam(':id_reaproveitado', trim($json['id_reaproveitado']), PDO::PARAM_INT);
            $stmt->bindParam(':pago', trim($json['pago']), PDO::PARAM_INT);
            $stmt->bindParam(':id_resultado_exame', trim($json['id_resultado_exame']), trim($json['id_resultado_exame']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':ativo', trim($json['ativo']), PDO::PARAM_INT);
            $stmt->bindParam(':id_rl_agendamento_exame', trim($json['id_rl_agendamento_exame']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exames do agendamento atualizados com sucesso!'
            );
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
                'result' => 'Exame já existente nesta agenda!'
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
