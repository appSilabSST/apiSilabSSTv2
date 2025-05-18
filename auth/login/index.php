<?php

include_once('../../conexao.php');
include_once('../jwt.php');

$postjson = json_decode(file_get_contents('php://input'), true);

// var_dump($postjson);exit;

$username = trim($postjson['username']);
$password = hash('sha256', trim($postjson['password']));
$remember = trim($postjson['rememberMe']) ?? 0;

// VERIFICA CAMPOS OBRIGATÓRIOS
if (empty($username) || empty($password)) {
    $result = json_encode(array(
        'success' => true
    ));
} else {
    $sql = "
    SELECT
        us.id_profissional,us.id_permissao,us.username,us.id_usuario_sistema,us.email,us.avatar,
        per.acesso,per.nome as perfil,
        IF(us.nome IS NULL, p.nome, us.nome) AS nome,
        ep.id_sala_atendimento,
        JSON_ARRAYAGG(sa.id_exame) AS ids_exames
    FROM 
        usuarios_sistema us
        LEFT JOIN profissionais p ON ( p.id_profissional = us.id_profissional)
        LEFT JOIN permissoes per ON ( per.id_permissao = us.id_permissao)
        LEFT JOIN especialidades es ON (es.id_especialidade = p.id_especialidade)
    	LEFT JOIN escalas_profissionais  ep ON (ep.id_profissional = p.id_profissional AND ep.`data` = CURDATE())
    	LEFT JOIN rl_salas_exames  sa ON (sa.id_sala_atendimento = ep.id_sala_atendimento)
    WHERE 
        us.username LIKE '$username'
        AND us.senha LIKE '$password'
        AND us.ativo = '1'
    GROUP BY 
	  	us.id_usuario_sistema  
    ";

    // echo $sql;exit;



    $query = mysqli_query($conecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_object($query);

        $name_fullname = explode(" ", $row->nome);
        $name_fullname = $name_fullname[0] . " " . end($name_fullname);

        $raizServidor = dirname($_SERVER['DOCUMENT_ROOT']);
        $img = $raizServidor . "/fotos/" . $row->avatar;

        // Verifica se o arquivo existe
        if (file_exists($img)) {
            $tipo = mime_content_type($img); // Detecta o tipo MIME, como image/jpeg
            $dados = base64_encode(file_get_contents($img));
            $avatarBase64 = "data:$tipo;base64,$dados";
        } else {
            $avatarBase64 =  ''; // Arquivo não encontrado
        }

        $user =
            [
                'nome' => $name_fullname,
                'id_profissional' => $row->id_profissional,
                'id_usuario_sistema' => $row->id_usuario_sistema,
                'email' => $row->email,
                'perfil' => $row->perfil,
                'acesso' => $row->acesso,
                'avatar' => $avatarBase64,
            ];

        $escala = [];

        if ($row->id_sala_atendimento > 0) {
            $escala = [
                'id_sala_atendimento' => $row->id_sala_atendimento,
                'ids_exames' => $row->ids_exames
            ];
        };

        $token = encodeJWT($remember, $user, $escala);
        // echo $token;exit;

        $result = json_encode(array(
            'success' => true,
            'token' => $token
        ));
    } else {
        $result = json_encode(array(
            'success' => true,
        ));
    }
}

http_response_code(200);

echo $result;
exit;
