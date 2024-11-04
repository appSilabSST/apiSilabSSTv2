<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_colaborador_empresa']) && is_numeric($json['id_rl_colaborador_empresa'])
        ) {

            $sql = "
            INSERT INTO afastamentos (id_rl_colaborador_empresa, data_entrega, data_afastamento, num_dias, data_retorno, cid, observacao) VALUES 
            (:id_rl_colaborador_empresa, :data_entrega, :data_afastamento, :num_dias, :data_retorno, :cid, :observacao)
            ";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_rl_colaborador_empresa', $json['id_rl_colaborador_empresa']);
            $stmt->bindParam(':data_entrega', trim($json['data_entrega']));
            $stmt->bindParam(':data_afastamento', trim($json['data_afastamento']));
            $stmt->bindParam(':num_dias', trim($json['num_dias']));
            $stmt->bindParam(':data_retorno', trim($json['data_retorno']), trim($json['data_retorno']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':cid', trim($json['cid']));
            $stmt->bindParam(':observacao', trim($json['observacao']), trim($json['observacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Afastamento salvo com sucesso!'
            );
        } else {
            http_response_code(400);
            $result = array(
                'status' => 'fail',
                'result' => 'Dados incompletos!'
            );
        }
    } catch (PDOException $ex) {
        $result = ["status" => "fail", "error" => $ex->getMessage()];
        http_response_code(200);
    } catch (Exception $ex) {
        $result = ["status" => "fail", "error" => $ex->getMessage()];
        http_response_code(200);
    } finally {
        $conn = null;
        echo json_encode($result);
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
