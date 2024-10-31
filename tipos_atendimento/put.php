<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $id_tipo_atendimento = $json['id'];
            $sql = "
            UPDATE tipos_atendimento SET
            cod_esocial = :cod_esocial, 
            tipo_atendimento = :tipo_atendimento
            WHERE id_tipo_atendimento = :id_tipo_atendimento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cod_esocial', trim($json['cod_esocial']), trim($json['cod_esocial']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tipo_atendimento', trim($json['tipo_atendimento']));
            $stmt->bindParam(':id_tipo_atendimento', $id_tipo_atendimento);
            $stmt->execute();
            
            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Tipo de atendimento atualizado com sucesso!'
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
