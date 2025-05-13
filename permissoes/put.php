<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id_permissao']) && is_numeric($json['id_permissao'])) {

            $sql = "
            UPDATE permissoes SET
            nome = :nome,
            acesso = :acesso
            WHERE id_permissao = :id_permissao
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nome', trim($json['nome']), PDO::PARAM_STR);
            $stmt->bindParam(':acesso', trim($json['acesso']), PDO::PARAM_STR);
            $stmt->bindParam(':id_permissao', trim($json['id_permissao']), PDO::PARAM_INT);
            $stmt->execute();

            $result = array(
                'status' => 'success',
                'result' => 'Cnae atualizado com sucesso!'
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
                'result' => 'Cnae já existente nesta proposta!'
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
