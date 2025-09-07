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


if (!$data || empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit();
}


$identifier = $data["email"];
$password = $data["password"];


$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR userid = ? LIMIT 1");
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $user["id"], 
                "userid" => $user["userid"], 
                "email" => $user["email"]
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid Email or UserID"]);
}

$stmt->close();
$conn->close();
?>
