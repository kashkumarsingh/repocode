<?php
// crud.php

// Load configuration
$config = require 'config.php'; // Load database configuration from a separate config file
$host = $config['db_host'];
$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];

// Establish database connection using PDO
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4"; // Data Source Name
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Set error mode to exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch results as associative arrays
    ]);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

// Handle CRUD operations based on the request method
$method = $_SERVER['REQUEST_METHOD']; // Get the request method (GET, POST, PUT, DELETE)

switch ($method) {
    case 'GET':
        // Read all users
        $stmt = $pdo->query("SELECT * FROM users"); // Execute the query to get all users
        $users = $stmt->fetchAll(); // Fetch all results as an array
        echo json_encode($users); // Encode the results to JSON and send to the client
        break;

    case 'POST':
        // Create a new user
        $data = json_decode(file_get_contents("php://input")); // Get raw POST data and decode JSON
        $name = $data->name; // Extract the name from the decoded object
        $email = $data->email; // Extract the email from the decoded object
        $password = password_hash($data->password, PASSWORD_BCRYPT); // Hash the password for security
        
        // Prepare the SQL statement to insert a new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        // Execute the statement with the provided values
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $password]);
        echo json_encode(['message' => 'User created successfully.']); // Send success message as JSON
        break;

    case 'PUT':
        // Update an existing user
        parse_str(file_get_contents("php://input"), $data); // Parse the incoming data
        $id = $data['id']; // Get user ID
        $name = $data['name']; // Get the new name
        $email = $data['email']; // Get the new email

        // Prepare the SQL statement to update the user
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        // Execute the statement with the new values
        $stmt->execute([':name' => $name, ':email' => $email, ':id' => $id]);
        echo json_encode(['message' => 'User updated successfully.']); // Send success message as JSON
        break;

    case 'DELETE':
        // Delete a user
        parse_str(file_get_contents("php://input"), $data); // Parse the incoming data
        $id = $data['id']; // Get user ID

        // Prepare the SQL statement to delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        // Execute the statement with the provided ID
        $stmt->execute([':id' => $id]);
        echo json_encode(['message' => 'User deleted successfully.']); // Send success message as JSON
        break;

    default:
        // Handle unsupported request methods
        echo json_encode(['message' => 'Method not allowed.']);
        break;
}
?>
