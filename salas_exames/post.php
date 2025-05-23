<?php

// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_sala_atendimento']) && isset($json['exames']) && count($json['exames']) > 0 && !in_array(null, $json['exames'])) {
            // Inicia a transação
            $conn->beginTransaction();

            // PREENCHE O AGENDAMENTO COM OS EXAMES ENVIADOS
            $sql = "
            INSERT INTO rl_salas_exames (id_sala_atendimento,id_exame) VALUES
            ";
            foreach ($json['exames'] as $key => $value) {
                $sql .= "(" . trim($json['id_sala_atendimento']) . ", :id_exame_$key
                ),";
            }
            $sql = substr($sql, 0, -1);  // Remove a última vírgula

            $stmt = $conn->prepare($sql);

            // Vincula os parâmetros
            foreach ($json['exames'] as $key => $value) {
                $stmt->bindParam(":id_exame_$key", trim($value));
            }

            // Executa a query
            $stmt->execute();

            // Verifica o sucesso da operação
            if ($stmt->rowCount() > 0) {
                $conn->commit(); // Confirma a transação
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Exames cadastrados com sucesso!'
                );
            } else {
                $conn->rollBack(); // Reverte a transação
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar os exames!'
                );
            }
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos ou inválidos!'
            );
        }
    } catch (\Throwable $th) {
        $conn->rollBack(); // Reverte a transação em caso de erro
        http_response_code(500);
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'Exame já existente neste agendamento!',
                'error' => $th->getMessage()
            );
        } else {
            $result = array(
                'status' => 'fail',
                'result' => 'Erro no banco de dados: ' . $th->getMessage()
            );
        }
    } finally {
        $conn = null; // Fecha a conexão
        if (!isset($result)) {
            $result = array(
                'status' => 'fail',
                'result' => 'Erro desconhecido!'
            );
        }
        echo json_encode($result); // Retorna a resposta
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
