<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

try {
    $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
    $database = getenv('MYSQL_DATABASE') ?: 'sensores_db';
    $user = getenv('MYSQL_USER') ?: 'root';
    $password = getenv('MYSQL_PASSWORD');
    if ($password === false) $password = '';

    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
    $limit = max(1, min($limit ?: 30, 100));
    $deviceId = filter_input(INPUT_GET, 'device_id', FILTER_VALIDATE_INT);
    if ($deviceId) {
        $stmt = $pdo->prepare('SELECT id, id_dispositivo, humedad, presion, temperatura, fecha_hora FROM mediciones WHERE id_dispositivo = :device_id ORDER BY fecha_hora DESC, id DESC LIMIT ' . $limit);
        $stmt->execute(['device_id' => $deviceId]);
    } else {
        $stmt = $pdo->query('SELECT id, id_dispositivo, humedad, presion, temperatura, fecha_hora FROM mediciones ORDER BY fecha_hora DESC, id DESC LIMIT ' . $limit);
    }

    echo json_encode(['ok' => true, 'measurements' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo conectar o consultar sensores_db. Revisá MySQL, las variables de entorno y las tablas.'], JSON_UNESCAPED_UNICODE);
}
