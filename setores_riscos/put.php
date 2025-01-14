<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['id_risco']) && is_numeric($json['id_risco']) &&
            isset($json['id_rl_setor_risco']) && is_numeric($json['id_rl_setor_risco'])
        ) {
            $sql = "
            UPDATE 
                rl_setores_riscos 
            SET
                id_profissional = :id_profissional, 
                id_setor = :id_setor, 
                id_risco = :id_risco, 
                id_tipo_exposicao = :id_tipo_exposicao,
                id_meio_propagacao = :id_meio_propagacao, 
                id_unidade_medida = :id_unidade_medida, 
                classificacao_agente = :classificacao_agente, 
                id_tipo_classificacao_agente= :id_tipo_classificacao_agente,
                id_tipo_avaliacao = :id_tipo_avaliacao, 
                intensidade = :intensidade, 
                limite_tolerancia = :limite_tolerancia, 
                tecnica_medicao = :tecnica_medicao, 
                fonte_geradora = :fonte_geradora, 
                medidas_controle = :medidas_controle, 
                exposicao = :exposicao, 
                controle = :controle, 
                gravidade = :gravidade, 
                pessoa_exposta = :pessoa_exposta, 
                probabilidade = :probabilidade, 
                severidade = :severidade, 
                epc_utiliza = :epc_utiliza, 
                epc_eficaz = :epc_eficaz, 
                epi_utiliza = :epi_utiliza, 
                epi_eficaz = :epi_eficaz, 
                medidas_protecao = :medidas_protecao, 
                condicoes_funcionamento = :condicoes_funcionamento, 
                uso_initerrupto = :uso_initerrupto, 
                prazo_validade = :prazo_validade, 
                periodicidade_troca = :periodicidade_troca, 
                higienizacao = :higienizacao, 
                codigo_gfip = :codigo_gfip, 
                insalubridade = :insalubridade, 
                periculosidade = :periculosidade,
                data_edit = NOW()
            WHERE 
                id_rl_setor_risco = :id_rl_setor_risco
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', $json['id_profissional']);
            $stmt->bindParam(':id_setor', $json['id_setor']);
            $stmt->bindParam(':id_risco', $json['id_risco']);
            $stmt->bindParam(':id_tipo_exposicao', $json['id_tipo_exposicao']);
            $stmt->bindParam(':id_meio_propagacao', $json['id_meio_propagacao']);
            $stmt->bindParam(':id_unidade_medida', $json['id_unidade_medida'], $json['id_unidade_medida'] == null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':classificacao_agente', $json['classificacao_agente'], $json['classificacao_agente'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_tipo_classificacao_agente', $json['id_tipo_classificacao_agente']);
            $stmt->bindParam(':id_tipo_avaliacao', $json['id_tipo_avaliacao']);
            $stmt->bindParam(':intensidade', $json['intensidade'], $json['intensidade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':limite_tolerancia', $json['limite_tolerancia'], $json['limite_tolerancia'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':tecnica_medicao', $json['tecnica_medicao'], $json['tecnica_medicao'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':fonte_geradora', $json['fonte_geradora'], $json['fonte_geradora'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':medidas_controle', $json['medidas_controle'], $json['medidas_controle'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':exposicao', $json['exposicao'], $json['exposicao'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':controle', $json['controle'], $json['controle'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':gravidade', $json['gravidade'], $json['gravidade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':pessoa_exposta', $json['pessoa_exposta'], $json['pessoa_exposta'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':probabilidade', $json['probabilidade'], $json['probabilidade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':severidade', $json['severidade'], $json['severidade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epc_utiliza', $json['epc_utiliza'], $json['epc_utiliza'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epc_eficaz', $json['epc_eficaz'], $json['epc_eficaz'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_utiliza', $json['epi_utiliza'], $json['epi_utiliza'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_eficaz', $json['epi_eficaz'], $json['epi_eficaz'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':medidas_protecao', $json['medidas_protecao'], $json['medidas_protecao'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':condicoes_funcionamento', $json['condicoes_funcionamento'], $json['condicoes_funcionamento'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':uso_initerrupto', $json['uso_initerrupto'], $json['uso_initerrupto'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':prazo_validade', $json['prazo_validade'], $json['prazo_validade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':periodicidade_troca', $json['periodicidade_troca'], $json['periodicidade_troca'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':higienizacao', $json['higienizacao'], $json['higienizacao'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':codigo_gfip', $json['codigo_gfip'], $json['codigo_gfip'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':insalubridade', $json['insalubridade'], $json['insalubridade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':periculosidade', $json['periculosidade'], $json['periculosidade'] == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_rl_setor_risco', trim($json['id_rl_setor_risco']));
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
