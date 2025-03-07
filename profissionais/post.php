<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['nome']) && isset($json['cpf']) && isset($json['id_especialidade'])) {

            $sql = "
            INSERT INTO profissionais (nome, cpf, id_especialidade, id_tipo_orgao, orgao_nr, orgao_uf, nit) VALUES
            (:nome, :cpf, :id_especialidade, :id_tipo_orgao, :orgao_nr, :orgao_uf, :nit)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':cpf', trim($json['cpf']));
            $stmt->bindParam(':id_especialidade', trim($json['id_especialidade']));
            $stmt->bindParam(':id_tipo_orgao', trim($json['id_tipo_orgao']), trim($json['id_tipo_orgao']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':orgao_nr', trim($json['orgao_nr']), trim($json['orgao_nr']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':orgao_uf', trim($json['orgao_uf']), trim($json['orgao_uf']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':nit', trim($json['nit']), trim($json['nit']) == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $result = array(
                    'status' => 'success',
                    'result' => 'Profissional cadastrado com sucesso!'
                );
            } else {
                http_response_code(500);
                $result = array(
                    'status' => 'fail',
                    'result' => 'Falha ao cadastrar profissional!'
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
