<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
header("Content-Type: application/json");


$host = "localhost";
$user = "root"; 
$pass = "";
$dbname = "inventory_system";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit();
}


$data = json_decode(file_get_contents("php://input"), true);


if (
    !$data || 
    empty($data["userid"]) || 
    empty($data["email"]) || 
    empty($data["password"])
) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit();
}

$userid = $data["userid"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);


$stmt = $conn->prepare("INSERT INTO users (userid, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $userid, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registered successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
