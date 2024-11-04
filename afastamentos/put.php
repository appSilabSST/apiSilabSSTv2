<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['id_rl_colaborador_empresa']) && is_numeric($json['id_rl_colaborador_empresa'])
        ) {
            $id_afastamento = $json['id'];
            $sql = "
            UPDATE afastamentos SET
            id_rl_colaborador_empresa = :id_rl_colaborador_empresa, 
            data_entrega = :data_entrega, 
            data_afastamento = :data_afastamento, 
            num_dias = :num_dias, 
            data_retorno = :data_retorno, 
            cid = :cid, 
            observacao = :observacao
            WHERE id_afastamento = :id_afastamento
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_colaborador_empresa', $json['id_rl_colaborador_empresa']);
            $stmt->bindParam(':data_entrega', trim($json['data_entrega']));
            $stmt->bindParam(':data_afastamento', trim($json['data_afastamento']));
            $stmt->bindParam(':num_dias', trim($json['num_dias']));
            $stmt->bindParam(':data_retorno', trim($json['data_retorno']), trim($json['data_retorno']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cid', trim($json['cid']));
            $stmt->bindParam(':observacao', trim($json['observacao']), trim($json['observacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_afastamento', trim($json['id_afastamento']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Afastamento atualizado com sucesso!'
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
