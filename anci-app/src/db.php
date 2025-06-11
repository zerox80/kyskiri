<?php
try {
    $host = 'aws-0-eu-central-1.pooler.supabase.com';
    $port = '6543';
    $dbname = 'postgres';
    $user = 'postgres.tornqhfqghfkhhkdsvkk'; 
    $password = 'suw1BqQnswVacnOC';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        "error" => "Database connection failed",
        "details" => $e->getMessage()
    ]);
    exit;
}
?>

