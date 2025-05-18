<?php
// VALIDA SE FOI LIBERADO O ACESSO
if ($authorization) {
    try {
        if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $id_usuario_sistema = trim($_GET["id"]);
            $sql = "SELECT us.id_usuario_sistema,us.avatar ,us.username, us.id_profissional,pp.nome as acesso, pp.id_permissao, p.*,
                           IF(us.nome IS NULL, p.nome, us.nome) AS nome
                    FROM usuarios_sistema us
                    LEFT JOIN profissionais p ON ( p.id_profissional = us.id_profissional)
                    LEFT JOIN permissoes pp ON (pp.id_permissao = us.id_permissao)
                    WHERE us.id_usuario_sistema = :id_usuario_sistema
                    AND us.ativo ='1'
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_usuario_sistema', $id_usuario_sistema);
        } else if (isset($_GET["id_profissional"]) && is_numeric($_GET["id_profissional"])) {
            $id_profissional = trim($_GET["id_profissional"]);
            $sql = "SELECT us.id_usuario_sistema, us.avatar ,us.username, us.id_profissional,pp.nome as acesso, pp.id_permissao, p.*,
                           IF(us.nome IS NULL, p.nome, us.nome) AS nome
                    FROM usuarios_sistema us
    	            LEFT JOIN profissionais p ON ( p.id_profissional = us.id_profissional)
                    LEFT JOIN permissoes pp ON (pp.id_permissao = us.id_permissao)
                    WHERE p.id_profissional = :id_profissional
                    AND us.ativo ='1'
                    ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_profissional', $id_profissional);
        } else if (isset($_GET["username"]) && !empty($_GET["username"])) { // Corrigido para verificar se username não está vazio
            $username = trim($_GET["username"]);
            $sql = "SELECT us.id_usuario_sistema,us.avatar , us.username, us.id_profissional,pp.nome as acesso, pp.id_permissao, p.*, 
                           IF(us.nome IS NULL, p.nome, us.nome) AS nome
                    FROM usuarios_sistema us
                    LEFT JOIN profissionais p ON ( p.id_profissional = us.id_profissional)
                    LEFT JOIN permissoes pp ON (pp.id_permissao = us.id_permissao)
                    WHERE us.username = :username
                    AND us.ativo = '1'"; // Adicionado filtro para usuários ativos
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
        } else {
            $sql = "SELECT us.id_usuario_sistema,us.avatar , us.username, us.id_profissional,us.ativo as ativoUsuario, pp.nome as acesso, pp.id_permissao, p.*,
                           IF(us.nome IS NULL, p.nome, us.nome) AS nome
                    FROM usuarios_sistema us
                    LEFT JOIN profissionais p ON ( p.id_profissional = us.id_profissional)
    	            LEFT JOIN permissoes pp ON (pp.id_permissao = us.id_permissao)
                    
            ";
            $stmt = $conn->prepare($sql);
        }

        // EXECUTAR SINTAXE SQL
        $stmt->execute();

        $result = getResult($stmt);
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
