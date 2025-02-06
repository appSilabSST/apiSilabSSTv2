<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_proposta']) && is_numeric($json['id_proposta']) &&
            isset($json['id_servico']) && is_numeric($json['id_servico']) &&
            isset($json['valor']) && is_numeric($json['valor']) &&
            isset($json['prazo']) && is_numeric($json['prazo'])
        ) {
            $sql = "
            INSERT INTO rl_propostas_servicos (id_proposta, id_servico, valor, prazo,observacoes) VALUES 
            (:id_proposta, :id_servico, :valor, :prazo,:observacoes)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_proposta', trim($json['id_proposta']));
            $stmt->bindParam(':observacoes', trim($json['observacoes']), isset($json['observacoes']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_servico', trim($json['id_servico']));
            $stmt->bindParam(':valor', trim($json['valor']));
            $stmt->bindParam(':prazo', trim($json['prazo']));
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Serviço cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar o serviço!'
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
                'result' => 'Serviço já existente nessa proposta!'
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
