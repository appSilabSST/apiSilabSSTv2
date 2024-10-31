<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (
            isset($json['id']) && is_numeric($json['id']) &&
            isset($json['nome']) && isset($json['cpf']) && isset($json['id_especialidade'])
            ) {
            $id_profissional = $json['id'];
            $sql = "
            UPDATE profissionais SET
            nome = :nome, 
            cpf = :cpf, 
            id_especialidade = :id_especialidade, 
            orgao_classe = :orgao_classe, 
            orgao_nr = :orgao_nr, 
            orgao_uf = :orgao_uf, 
            nit = :nit
            WHERE id_profissional = :id_profissional
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':cpf', trim($json['cpf']));
            $stmt->bindParam(':id_especialidade', trim($json['id_especialidade']));
            $stmt->bindParam(':orgao_classe', trim($json['orgao_classe']), trim($json['orgao_classe']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':orgao_nr', trim($json['orgao_nr']), trim($json['orgao_nr']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':orgao_uf', trim($json['orgao_uf']), trim($json['orgao_uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':nit', trim($json['nit']), trim($json['nit']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_profissional', $id_profissional);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Profissional atualizado com sucesso!'
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
        // DADOS ÚNICOS JÁ UTILIZADOS
        if ($th->getCode() == 23000) {
            $result = array(
                'status' => 'fail',
                'result' => 'CPF já existente!',
                'error' => $th->getMessage()
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
