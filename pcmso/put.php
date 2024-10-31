<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE pcmso SET
            id_empresa = :id_empresa, 
            id_status_documento = :id_status_documento, 
            id_local_atividade = :id_local_atividade, 
            id_profissional = :id_profissional, 
            data_inicio = :data_inicio, 
            data_fim = :data_fim, 
            responsavel = :responsavel, 
            responsavel_cpf = :responsavel_cpf, 
            responsavel_email = :responsavel_email
            WHERE id_pcmso = :id_pcmso
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_status_documento', trim($json['id_status_documento']));
            $stmt->bindParam(':id_local_atividade', trim($json['id_local_atividade']));
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']));
            $stmt->bindParam(':data_inicio', trim($json['data_inicio']));
            $stmt->bindParam(':data_fim', trim($json['data_fim']));
            $stmt->bindParam(':responsavel', trim($json['responsavel']));
            $stmt->bindParam(':responsavel_cpf', trim($json['responsavel_cpf']));
            $stmt->bindParam(':responsavel_email', trim($json['responsavel_email']));
            $stmt->bindParam(':id_pcmso', trim($json['id']));
            $stmt->execute();
            
            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'PCMSO atualizado com sucesso!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
