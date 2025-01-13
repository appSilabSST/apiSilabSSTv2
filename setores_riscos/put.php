<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['id_risco']) && is_numeric($json['id_risco'])
        ) {
            $sql = "
            UPDATE rl_setores_riscos SET
            id_profissional = :id_profissional, 
            id_setor = :id_setor, 
            id_risco = :id_risco, 
            classificacao_agente = :classificacao_agente, 
            tipo_avaliacao = :tipo_avaliacao, 
            intensidade = :intensidade, 
            limite_tolerancia = :limite_tolerancia, 
            id_unidade_medida = :id_unidade_medida, 
            exposicao = :exposicao, 
            controle = :controle, 
            gravidade = :gravidade, 
            pessoa_exposta = :pessoa_exposta, 
            fonte_geradora = :fonte_geradora, 
            tecnica_medicao = :tecnica_medicao, 
            id_meio_propagacao = :id_meio_propagacao, 
            medidas_controle = :medidas_controle, 
            severidade = :severidade, 
            probabilidade = :probabilidade, 
            id_classificacao_risco = :id_classificacao_risco, 
            epc_utiliza = :epc_utiliza, 
            epc_eficaz = :epc_eficaz, 
            epi_utiliza = :epi_utiliza, 
            epi_eficaz = :epi_eficaz, 
            condicoes_funcionamento = :condicoes_funcionamento, 
            condicoes_funcionamento = :condicoes_funcionamento, 
            uso_initerrupto = :uso_initerrupto, 
            prazo_validade = :prazo_validade, 
            periodicidade_troca = :periodicidade_troca, 
            higienizacao = :higienizacao, 
            codigo_gfip = :codigo_gfip, 
            insalubridade = :insalubridade, 
            periculosidade = :periculosidade,
            data_edit = NOW()
            WHERE id_rl_setor_risco = :id_rl_setor_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', trim($json['id_profissional']), trim($json['id_profissional']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_setor', trim($json['id_setor']));
            $stmt->bindParam(':id_risco', trim($json['id_risco']));
            $stmt->bindParam(':classificacao_agente', trim($json['classificacao_agente']), trim($json['classificacao_agente']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tipo_avaliacao', trim($json['tipo_avaliacao']), trim($json['tipo_avaliacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':intensidade', trim($json['intensidade']), trim($json['intensidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':limite_tolerancia', trim($json['limite_tolerancia']), trim($json['limite_tolerancia']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_unidade_medida', trim($json['id_unidade_medida']), trim($json['id_unidade_medida']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_tipo_exposicao', trim($json['id_tipo_exposicao']), trim($json['id_tipo_exposicao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':exposicao', trim($json['exposicao']), trim($json['exposicao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':controle', trim($json['controle']), trim($json['controle']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':gravidade', trim($json['gravidade']), trim($json['gravidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':pessoa_exposta', trim($json['pessoa_exposta']), trim($json['pessoa_exposta']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':fonte_geradora', trim($json['fonte_geradora']), trim($json['fonte_geradora']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tecnica_medicao', trim($json['tecnica_medicao']), trim($json['tecnica_medicao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_meio_propagacao', trim($json['id_meio_propagacao']), trim($json['id_meio_propagacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':medidas_controle', trim($json['medidas_controle']), trim($json['medidas_controle']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':severidade', trim($json['severidade']), trim($json['severidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':probabilidade', trim($json['probabilidade']), trim($json['probabilidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_classificacao_risco', trim($json['id_classificacao_risco']), trim($json['id_classificacao_risco']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epc_utiliza', trim($json['epc_utiliza']), trim($json['epc_utiliza']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epc_eficaz', trim($json['epc_eficaz']), trim($json['epc_eficaz']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_utiliza', trim($json['epi_utiliza']), trim($json['epi_utiliza']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_eficaz', trim($json['epi_eficaz']), trim($json['epi_eficaz']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':medidas_protecao', trim($json['medidas_protecao']), trim($json['medidas_protecao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':condicoes_funcionamento', trim($json['condicoes_funcionamento']), trim($json['condicoes_funcionamento']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uso_initerrupto', trim($json['uso_initerrupto']), trim($json['uso_initerrupto']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':prazo_validade', trim($json['prazo_validade']), trim($json['prazo_validade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':periodicidade_troca', trim($json['periodicidade_troca']), trim($json['periodicidade_troca']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':higienizacao', trim($json['higienizacao']), trim($json['higienizacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':codigo_gfip', trim($json['codigo_gfip']), trim($json['codigo_gfip']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':insalubridade', trim($json['insalubridade']), trim($json['insalubridade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':periculosidade', trim($json['periculosidade']), trim($json['periculosidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_rl_setor_risco', trim($json['id']));
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Risco atualizado com sucesso!'
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
