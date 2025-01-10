<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_empresa']) && is_numeric($json['id_empresa']) && isset($json['id_tipo_ambiente']) && is_numeric($json['id_tipo_ambiente'])) {
            $sql = "
            INSERT INTO locais_atividade (id_empresa, id_tipo_ambiente,id_empresa_local_atividade, atividade_principal) VALUES 
            (:id_empresa, :id_tipo_ambiente, :id_empresa_local_atividade, :atividade_principal)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_empresa', trim($json['id_empresa']));
            $stmt->bindParam(':id_tipo_ambiente', trim($json['id_tipo_ambiente']));
            $stmt->bindParam(':id_empresa_local_atividade', trim($json['id_empresa_local_atividade']), isset($json['id_empresa_local_atividade']) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':atividade_principal', trim($json['atividade_principal']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Local de atividade criado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao criar o local de atividade!'
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
                'result' => 'Local de atividade já existente!'
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
