<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include('../conn.php');

try {
    if (isset($_GET["token"])) {
        $token = trim($_GET["token"]);
        $sql = "
            SELECT *
            FROM token
            WHERE ativo = '1'
            AND token = :token
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);
    }

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_OBJ);
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
