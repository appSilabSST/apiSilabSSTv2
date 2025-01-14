<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_rl_setor_risco_plano_acao']) && is_numeric($json['id_rl_setor_risco_plano_acao']) &&
            isset($json['id_rl_setor_risco']) && is_numeric($json['id_rl_setor_risco']) &&
            isset($json['plano_acao']) && isset($json['descricao'])
        ) {
            $sql = "
            UPDATE rl_setores_riscos_planos_acao SET
            id_pgr = :id_pgr, 
            id_rl_setor_risco = :id_rl_setor_risco, 
            plano_acao = :plano_acao, 
            descricao = :descricao, 
            medida_suficiente = :medida_suficiente, 
            data_avaliacao = :data_avaliacao, 
            indicacao_medida = :indicacao_medida, 
            data_edit = NOW()
            WHERE id_rl_setor_risco_plano_acao = :id_rl_setor_risco_plano_acao
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pgr', trim($json['id_pgr']), isset($json['id_pgr']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_rl_setor_risco', trim($json['id_rl_setor_risco']), PDO::PARAM_INT);
            $stmt->bindParam(':plano_acao', trim($json['plano_acao']), PDO::PARAM_STR);
            $stmt->bindParam(':descricao', trim($json['descricao']), isset($json['descricao']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':medida_suficiente', trim($json['medida_suficiente']), PDO::PARAM_INT);
            $stmt->bindParam(':data_avaliacao', trim($json['data_avaliacao']), isset($json['data_avaliacao']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':indicacao_medida', trim($json['indicacao_medida']), isset($json['indicacao_medida']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':id_rl_setor_risco_plano_acao', trim($json['id_rl_setor_risco_plano_acao']), PDO::PARAM_INT);;
            $stmt->execute();

            http_response_code(200);
            $result = 'Plano de ação atualizado com sucesso!';
        } else {
            http_response_code(400);
            $result = 'Dados incompletos!';
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = 'Plano de ação já existente!';
        } else {
            $result = $th->getMessage();
        }
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
