<?php
$name = $email = "";
$errors = [];
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    } elseif (!preg_match("/[!@#$%^&*]/", $password)) {
        $errors['password'] = "Password must contain at least one special character (!@#$%^&*)";
    }

    // Confirm password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    if (empty($errors)) {
        $file = "users.json";

        
        if (!file_exists($file)) {
            $errors['file'] = "Error: users.json file not found.";
        } else {
            
            $json_data = file_get_contents($file);

            if ($json_data === false) {
                $errors['file'] = "Error reading users.json file.";
            } else {
                $users = json_decode($json_data, true);

                if (!is_array($users)) {
                    $users = [];
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $new_user = [
                    "name" => $name,
                    "email" => $email,
                    "password" => $hashed_password
                ];

                
                $users[] = $new_user;

                $json_save = json_encode($users, JSON_PRETTY_PRINT);

                if (file_put_contents($file, $json_save) === false) {
                    $errors['file'] = "Error writing to users.json";
                } else {
                    $success = "Registration successful!";
                    // Clear form fields
                    $name = $email = "";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <style>
        .error { color: red; font-size: 14px; }
        .success { color: green; font-size: 16px; margin-bottom: 15px; }
        form { width: 300px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; width: 100%; }
    </style>
</head>

<body>
    <h2 style="text-align:center;">User Registration</h2>

    <form method="POST" action="">
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <label>Name:</label>
        <input type="text" name="name" value="enter name">
        <div class="error"><?php echo $errors['name'] ?? ""; ?></div>

        <label>Email:</label>
        <input type="text" name="email" value="enter gmail">
        <div class="error"><?php echo $errors['email'] ?? ""; ?></div>

        <label>Password:</label>
        <input type="password" name="password">
        <div class="error"><?php echo $errors['password'] ?? ""; ?></div>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password">
        <div class="error"><?php echo $errors['confirm_password'] ?? ""; ?></div>

        <div class="error"><?php echo $errors['file'] ?? ""; ?></div>

        <button type="submit">Register</button>
    </form>
</body>
</html>