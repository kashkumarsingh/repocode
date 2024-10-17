<?php
// process-form.php

// Load configuration
// This includes the 'config.php' file which returns an array of configuration settings. 
// By separating configurations, we make the code more maintainable and secure.
$config = require 'config.php';

// Extract database settings from config array
// We pull each specific setting from the config array for ease of use in our PDO connection setup.
$host = $config['db_host'];
$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];

// Check if the request method is POST, meaning the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define and sanitize input variables
    // We trim whitespace from user inputs to ensure no extraneous spaces are included.
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    // If any of the inputs are empty, we terminate the script with an error message.
    // This is a basic check to ensure all required fields are filled out.
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Filter and validate inputs
    // The name is sanitized to remove unwanted characters. FILTER_SANITIZE_STRING ensures 
    // that only valid characters are retained.
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    // The email is validated to check if it has the correct email format.
    // If validation fails, the script stops and displays an error.
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if (!$email) {
        die("Invalid email format.");
    }

    // Hash the password securely
    // password_hash() is used to hash the password with a strong one-way hashing algorithm.
    // The PASSWORD_BCRYPT option provides robust security against brute-force attacks.
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Establish a database connection using PDO
    try {
        // Data Source Name (DSN) string that tells PDO where and how to connect to the database
        // We specify the character encoding (utf8mb4) to support full Unicode, including emojis.
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        // Create a new PDO instance for the database connection
        // The options array provides settings to handle errors as exceptions and sets the default 
        // fetch mode for queries to associative arrays, making data easier to work with.
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Prepare the SQL query for insertion
        // This SQL statement uses placeholders (:name, :email, :password) for user inputs. 
        // Prepared statements help prevent SQL injection by separating the query structure from the data.
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $pdo->prepare($sql);

        // Execute the prepared statement with an array of values for each placeholder
        // This binds the values safely and securely to the SQL query.
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        // Prepare to send a confirmation email to the user
        // The email headers include the From and Reply-To addresses, which help identify 
        // the sender and handle replies.
        $to = $email;
        $subject = "Registration Confirmation";
        $message = "Hello $name,\n\n Thank you for registering with us!\n\nName: $name\nEmail: $email";
        $headers = "From: noreply@example.com\r\n" .
            "Reply-To: noreply@example.com\r\n" .
            "Content-type: text/plain; charset=utf-8\r\n";
        // Attempt to send the email and provide feedback
        // If the mail() function succeeds, we confirm registration success and email delivery. 
        // Otherwise, we log an error to a file and display a message indicating partial success.


        if(mail($to, $subject, $message, $headers)){
            echo "Registration successful! A confirmation email has been sent to $email.";
        }else {
            error_log("Failed to send email to $email");
            echo "Registration successful, but confirmation email could not be sent.";
        }


    } catch (PDOException $e) {
        // Log the PDO error to help with debugging and avoid exposing sensitive details
        // This ensures sensitive database error details are not shown to the user.
        error_log("Database error: " . $e->getMessage());
        die("An error occurred while processing your request.");
    }
}
