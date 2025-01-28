<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    $id_formulario = trim($json['id_formulario']);
    $id_componente = trim($json['tipo']);
    $label = trim($json['label']);
    $options = $json['options'];
    $ordem = $json["ordem"];
    $tamanho = $json["tamanho"];

    try {
        if (empty($id_formulario) || empty($id_componente) || empty($label)) {
            throw new ErrorException("Campos não preenchido!", 1);
        }

        // Inserir na tabela
        $insert_rl_formularios_componentes = "INSERT INTO rl_formularios_componentes (id_formulario, id_componente, label, ordem, tamanho) VALUES (:id_formulario, :id_componente, :label, :ordem, :tamanho)";

        $stmt = $conn->prepare($insert_rl_formularios_componentes);
        $stmt->bindParam(':id_formulario', $id_formulario);
        $stmt->bindParam(':id_componente', $id_componente);
        $stmt->bindParam(':label', $label);
        $stmt->bindParam(':ordem', $ordem);
        $stmt->bindParam(':tamanho', $tamanho);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception($errorInfo[2]);
        }

        // Obter o ID do último registro
        $id_rl_formulario_componente = $conn->lastInsertId();

        if (!is_array($options)) {
            $options = [];
        }

        if ($options) {
            $insert_rl_componentes_opcoes = "INSERT INTO rl_componentes_opcoes (id_rl_formulario_componente, opcao, ordem) VALUES (:id_rl_formulario_componente, :opcao, :ordem)";
            $stmt = $conn->prepare($insert_rl_componentes_opcoes);

            foreach ($options as $i => $option) {
                $stmt->bindParam(':id_rl_formulario_componente', $id_rl_formulario_componente);
                $stmt->bindParam(':opcao', $option);
                $stmt->bindValue(':ordem', $i + 1, PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $errorInfo = $stmt->errorInfo();
                    throw new Exception($errorInfo[2]);
                }
            }
        }

        $result = ["status" => "success"];
    } catch (\Throwable $th) {
        $conn->rollBack();
        http_response_code(500);
        $result = [
            'status' => 'fail',
            'result' => $th->getCode() == 23000 ? 'Risco já existente neste agendamento!' : $th->getMessage(),
            'error' => $th->getMessage()
        ];
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
