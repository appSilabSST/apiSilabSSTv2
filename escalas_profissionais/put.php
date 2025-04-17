<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_escala_profissional']) && is_numeric($json['id_escala_profissional']) &&
            isset($json['id_profissional']) && is_numeric($json['id_profissional']) &&
            isset($json['id_sala_atendimento']) && is_numeric($json['id_sala_atendimento']) &&
            isset($json['data'])
        ) {

            $sql = "
            UPDATE escalas_profissionais SET
            data = :data,
            id_profissional = :id_profissional,  
            id_sala_atendimento = :id_sala_atendimento  
            WHERE id_escala_profissional = :id_escala_profissional
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', trim($json['data']));
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), PDO::PARAM_INT);
            $stmt->bindParam(':id_sala_atendimento', trim($json['id_sala_atendimento']), PDO::PARAM_INT);
            $stmt->bindParam(':id_escala_profissional', trim($json['id_escala_profissional']), PDO::PARAM_INT);
      
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Cnae atualizado com sucesso!'
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
                'result' => 'Cnae já existente nesta proposta!'
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
