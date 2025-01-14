<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_setor_risco']) && is_numeric($json['id_rl_setor_risco']) &&
            isset($json['plano_acao']) && isset($json['descricao'])
        ) {

            $sql = "
            INSERT INTO rl_setores_riscos_planos_acao (id_pgr, id_rl_setor_risco, plano_acao, descricao, medida_suficiente, data_avaliacao, indicacao_medida) VALUES
            (:id_pgr, :id_rl_setor_risco, :plano_acao, :descricao, :medida_suficiente, :data_avaliacao, :indicacao_medida)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pgr', trim($json['id_pgr']), isset($json['id_pgr']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_rl_setor_risco', trim($json['id_rl_setor_risco']), PDO::PARAM_INT);
            $stmt->bindParam(':plano_acao', trim($json['plano_acao']), PDO::PARAM_STR);
            $stmt->bindParam(':descricao', trim($json['descricao']), isset($json['descricao']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':medida_suficiente', trim($json['medida_suficiente']), isset($json['medida_suficiente']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':data_avaliacao', trim($json['data_avaliacao']), isset($json['data_avaliacao']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':indicacao_medida', trim($json['indicacao_medida']), isset($json['indicacao_medida']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = 'Plano de ação cadastrado com sucesso!';
            } else {
                http_response_code(500);
                $result = 'Falha ao cadastrar plano de ação!';
            }
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Plano de ação já existente!';
            $result = $th->getMessage();
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
