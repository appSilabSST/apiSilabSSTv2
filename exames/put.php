<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_exame']) && is_numeric($json['id_exame'])) {
            $id_exame = $json['id_exame'];
            $sql = "
            UPDATE exames SET 
                padronizar = :padronizar, 
                id_fornecedor = :id_fornecedor, 
                valor_cobrar = :valor_cobrar, 
                validade = :validade, 
                valor_custo = :valor_custo
            WHERE id_exame = :id_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':padronizar', trim($json['padronizar']));
            $stmt->bindParam(':id_fornecedor', trim($json['id_fornecedor']), trim($json['id_fornecedor']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':valor_cobrar', trim($json['valor_cobrar']), trim($json['valor_cobrar']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':validade', trim($json['validade']), trim($json['validade']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':valor_custo', trim($json['valor_custo']), trim($json['valor_custo']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id_exame', trim($json['id_exame']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Exame atualizado com sucesso!'
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
