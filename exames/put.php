<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $id_exame = $json['id'];
            $sql = "
            UPDATE exames SET
            cod_esocial = :cod_esocial, 
            procedimento = :procedimento, 
            id_fornecedor = :id_fornecedor, 
            valor_cobrar = :valor_cobrar, 
            validade = :validade, 
            valor_custo = :valor_custo, 
            valor_desconto = :valor_desconto
            WHERE id_exame = :id_exame
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cod_esocial', trim($json['cod_esocial']), trim($json['cod_esocial']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':procedimento', trim($json['procedimento']));
            $stmt->bindParam(':id_fornecedor', trim($json['id_fornecedor']), trim($json['id_fornecedor']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':valor_cobrar', trim($json['valor_cobrar']), trim($json['valor_cobrar']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':validade', trim($json['validade']), trim($json['validade']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':valor_custo', trim($json['valor_custo']), trim($json['valor_custo']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':valor_desconto', trim($json['valor_desconto']), trim($json['valor_desconto']) == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
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
