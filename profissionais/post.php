<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['nome']) && !empty($json['nome'])) {

            $sql = "
            INSERT INTO profissionais (nome, cpf, id_especialidade, numero,estado,nit) VALUES
            (:nome, :cpf, :id_especialidade,:numero,:estado,:nit)
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', trim($json['nome']));
            $stmt->bindParam(':cpf', trim($json['cpf']), empty($json['nome']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_especialidade', trim($json['id_especialidade']), empty($json['id_especialidade']) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':numero', trim($json['numero']), empty($json['numero']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':estado', trim($json['estado']));
            $stmt->bindParam(':nit', trim($json['nit']));
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
