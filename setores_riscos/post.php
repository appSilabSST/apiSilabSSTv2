<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id_setor']) && is_numeric($json['id_setor']) &&
            isset($json['id_risco']) && is_numeric($json['id_risco'])
        ) {

            $sql = "
            INSERT INTO rl_setores_riscos (id_profissional, id_setor, id_risco, classificacao_agente, tipo_avaliacao, intensidade, limite_tolerancia, id_unidade_medida, id_tipo_exposicao, fonte_geradora, tecnica_medicao, id_meio_propagacao, medidas_controle, severidade, probabilidade, id_classificacao_risco, epc_utiliza, epc_eficaz, epi_utiliza, epi_eficaz, epi_medProtecao, epi_condFuncto, epi_usoInint, epi_przValid, epi_periodicTroca, epi_higienizacao, codigo_gfip, insalubridade, periculosidade) VALUES
            (:id_profissional, :id_setor, :id_risco, :classificacao_agente, :tipo_avaliacao, :intensidade, :limite_tolerancia, :id_unidade_medida, :id_tipo_exposicao, :fonte_geradora, :tecnica_medicao, :id_meio_propagacao, :medidas_controle, :severidade, :probabilidade, :id_classificacao_risco, :epc_utiliza, :epc_eficaz, :epi_utiliza, :epi_eficaz, :epi_medProtecao, :epi_condFuncto, :epi_usoInint, :epi_przValid, :epi_periodicTroca, :epi_higienizacao, :codigo_gfip, :insalubridade, :periculosidade)
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
            $stmt->bindParam(':epi_medProtecao', trim($json['epi_medProtecao']), trim($json['epi_medProtecao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_condFuncto', trim($json['epi_condFuncto']), trim($json['epi_condFuncto']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_usoInint', trim($json['epi_usoInint']), trim($json['epi_usoInint']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_przValid', trim($json['epi_przValid']), trim($json['epi_przValid']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_periodicTroca', trim($json['epi_periodicTroca']), trim($json['epi_periodicTroca']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':epi_higienizacao', trim($json['epi_higienizacao']), trim($json['epi_higienizacao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':codigo_gfip', trim($json['codigo_gfip']), trim($json['codigo_gfip']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':insalubridade', trim($json['insalubridade']), trim($json['insalubridade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':periculosidade', trim($json['periculosidade']), trim($json['periculosidade']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Risco cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar risco!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
    } finally {
        $conn = null;
        echo json_encode($result);
    }
}
