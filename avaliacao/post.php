<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_agendamento_exame']) && is_numeric($json['id_rl_agendamento_exame']) &&
            isset($json['resultado']) && is_numeric($json['resultado'])
        ) {
            
            // Verifica se 'avaliacao' é um objeto JSON válido
            if (isset($json['avaliacao']) && is_array($json['avaliacao'])) {
                // Convertendo o array para uma string JSON válida
                $avaliacao = json_encode($json['avaliacao']);
            } else {
                // Se não for válido, atribui um objeto vazio
                $avaliacao = '{}'; // Representa um JSON vazio
            }

            // Prepara o SQL para inserir a avaliação
            $sql = "
            INSERT INTO avaliacao (id_rl_agendamento_exame, avaliacao, resultado, anotacao) 
            VALUES (:id_rl_agendamento_exame, :avaliacao, :resultado, :anotacao)
            ";

            // Prepara a consulta SQL
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_agendamento_exame', trim($json['id_rl_agendamento_exame']), PDO::PARAM_INT);
            $stmt->bindParam(':resultado', trim($json['resultado']), PDO::PARAM_INT);
            $stmt->bindParam(':avaliacao', $avaliacao);  // Passa a string JSON para o banco de dados
            $stmt->bindParam(':anotacao', trim($json['anotacao']));
            $stmt->execute();

            // Verifica se a inserção foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                $id_cnae = $conn->lastInsertId();

                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Avaliação registrada com sucesso!',
                    'id' => $id_cnae
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao salvar Avaliação!'
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
                'result' => 'Avaliação já existente!',
                'error' => $th->getMessage()
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
}
