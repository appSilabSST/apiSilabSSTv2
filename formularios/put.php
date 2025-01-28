<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($json['id']) && is_numeric($json['id'])) {
            $sql = "
            UPDATE 
                formularios 
            SET
                formulario = :formulario,
                status = :status,
                linhas = :linhas,
                descricao = :descricao
            WHERE 
                id_formulario = :id_formulario
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':formulario', trim($json['formulario']));
            $stmt->bindParam(':descricao', trim($json['descricao']));
            $stmt->bindParam(':status', trim($json['status']), PDO::PARAM_INT);
            $stmt->bindParam(':linhas', trim($json['linhas']), PDO::PARAM_INT);
            $stmt->bindParam(':id_formulario', trim($json['id']), PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            $result = array(
                'status' => 'success',
                'result' => 'Atualizados com sucesso!'
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
        $result = array(
            'status' => 'fail',
            'result' => $th->getMessage()
        );
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
