<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection
$host = 'sql307.infinityfree.com';
$db = 'if0_38357642_mufac_rest_api';
$user = 'if0_38357642'; 
$pass = '22694Wcc123'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to send JSON response
function sendResponse($status, $data = null, $message = null) {
    http_response_code($status);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// GET all users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        sendResponse(200, $users,'user(s) found');
    } else {
        sendResponse(404, null, 'No users found');
    }
}

// GET a single user by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        sendResponse(200, $user,'user found');
    } else {
        sendResponse(404, null, 'User not found');
    }
}

// POST a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id']) || empty($data['name']) || empty($data['email'])) {
        sendResponse(400, null, 'id, name and email are required');
    }

    $id = $data['id'];
    $name = $data['name'];
    $email = $data['email'];

    $sql = "INSERT INTO users (id, name, email) VALUES ('$id', '$name', '$email')";
    if ($conn->query($sql) === TRUE) {
       // $newUserId = $conn->insert_id;
        sendResponse(201, ['id' => $id, 'name' => $name, 'email' => $email], 'User created successfully');
    } else {
        sendResponse(500, null, 'Error creating user: ' . $conn->error);
    }
}

// PUT a user by ID
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id']) || empty($data['name']) || empty($data['email'])) {
        sendResponse(400, null, 'ID, name, and email are required');
    }

    $id = $data['id'];
    $name = $data['name'];
    $email = $data['email'];

    $sql = "UPDATE users SET name = '$name', email = '$email' WHERE id = $id";
    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) {
            sendResponse(200, ['id' => $id, 'name' => $name, 'email' => $email], 'User updated successfully');
        } else {
            sendResponse(404, null, 'User not found');
        }
    } else {
        sendResponse(500, null, 'Error updating user: ' . $conn->error);
    }
}

// DELETE a user by ID
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {    
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = $id";
    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) {
            sendResponse(200, null, 'User deleted successfully');
        } else {
            sendResponse(404, null, 'User not found');
        }
    } else {
        sendResponse(500, null, 'Error deleting user: ' . $conn->error);
    }        
} else {
    sendResponse(400, null, 'ID is required');
}

// Close the database connection
$conn->close();
?>