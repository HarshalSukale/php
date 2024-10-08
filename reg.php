<?php
include 'db.php'; // Ensure this file contains your database connection code

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define variables and set them to empty values
    $id = $username = $email = $phone = $dob = $gender = $address = $course = $password = $confirmPassword = "";
    $errors = [];

    // Validation functions
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate ID
    if (empty($_POST["id"])) {
        $errors['id'] = "ID is required";
    } else {
        $id = sanitizeInput($_POST["id"]);
        if (!preg_match("/^[0-9]+$/", $id)) {
            $errors['id'] = "Invalid ID format";
        }
    }

    // Validate Username
    if (empty($_POST["username"])) {
        $errors['username'] = "Username is required";
    } else {
        $username = sanitizeInput($_POST["username"]);
    }

    // Validate Email
    if (empty($_POST["email"])) {
        $errors['email'] = "Email is required";
    } else {
        $email = sanitizeInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }
    }

    // Validate Phone Number
    if (empty($_POST["phone"])) {
        $errors['phone'] = "Phone number is required";
    } else {
        $phone = sanitizeInput($_POST["phone"]);
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            $errors['phone'] = "Invalid phone number format";
        }
    }

    // Validate Date of Birth
    if (empty($_POST["dob"])) {
        $errors['dob'] = "Date of birth is required";
    } else {
        $dob = sanitizeInput($_POST["dob"]);
    }

    // Validate Gender
    if (empty($_POST["gender"])) {
        $errors['gender'] = "Gender is required";
    } else {
        $gender = sanitizeInput($_POST["gender"]);
    }

    // Validate Address
    if (empty($_POST["address"])) {
        $errors['address'] = "Address is required";
    } else {
        $address = sanitizeInput($_POST["address"]);
    }

    // Validate Course
    if (empty($_POST["course"])) {
        $errors['course'] = "Course of study is required";
    } else {
        $course = sanitizeInput($_POST["course"]);
    }

    // Validate Password and Confirm Password
    if (isset($_POST["password"]) && empty($_POST["password"])) {
        $errors['password'] = "Password is required";
    } else {
        $password = sanitizeInput($_POST["password"]);
    }

    if (isset($_POST["confirmPassword"]) && empty($_POST["confirmPassword"])) {
        $errors['confirmPassword'] = "Confirming your password is required";
    } else {
        $confirmPassword = sanitizeInput($_POST["confirmPassword"]);
        if ($password !== $confirmPassword) {
            $errors['confirmPassword'] = "Passwords do not match";
        }
    }

    // Check if there are no errors
    if (empty($errors)) {
        // Create Operation
        if (isset($_POST['action']) && $_POST['action'] == 'create') {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashing the password
            $sql = "INSERT INTO students (id, username, email, phone, dob, gender, address, course, password)
                    VALUES ('$id', '$username', '$email', '$phone', '$dob', '$gender', '$address', '$course', '$hashedPassword')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $conn->error;
            }
        }

        // Update Operation
        if (isset($_POST['action']) && $_POST['action'] == 'update') {
            $sql = "UPDATE students SET username='$username', email='$email', phone='$phone', dob='$dob', gender='$gender', address='$address', course='$course' WHERE id='$id'";

            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }

        // Delete Operation
        if (isset($_POST['action']) && $_POST['action'] == 'delete') {
            $sql = "DELETE FROM students WHERE id='$id'";

            if ($conn->query($sql) === TRUE) {
                echo "Record deleted successfully";
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }

        // Read Operation
        $sql = "SELECT * FROM students";
        $result = $conn->query($sql);

        if ($result === FALSE) {
            echo "Error: " . $conn->error;
        } elseif ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "ID: " . $row["id"] . " - Name: " . $row["username"] . " - Email: " . $row["email"] . "<br>";
            }
        } else {
            echo "0 results";
        }
    } else {
        // Display errors
        foreach ($errors as $key => $error) {
            echo "<p>$key: $error</p>";
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            font-size: 0.875em;
            margin-bottom: 10px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
        }

        .checkbox-label input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Registration Form</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Hidden input to specify the action -->
            <input type="hidden" name="action" id="form-action" value="create">

            <label for="id">Student ID:</label>
            <input type="text" id="id" name="id" required>
            <span class="error"><?php echo $errors['id'] ?? ''; ?></span>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <span class="error"><?php echo $errors['username'] ?? ''; ?></span>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="error"><?php echo $errors['email'] ?? ''; ?></span>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
            <span class="error"><?php echo $errors['phone'] ?? ''; ?></span>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <span class="error"><?php echo $errors['dob'] ?? ''; ?></span>

            <label>Gender:</label>
            <label><input type="radio" name="gender" value="male" required> Male</label>
            <label><input type="radio" name="gender" value="female"> Female</label>
            <label><input type="radio" name="gender" value="other"> Other</label>
            <span class="error"><?php echo $errors['gender'] ?? ''; ?></span>

            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="4" required></textarea>
            <span class="error"><?php echo $errors['address'] ?? ''; ?></span>

            <label for="course">Course of Study:</label>
            <select id="course" name="course" required>
                <option value="">Select a course</option>
                <option value="computer_science">Computer Science</option>
                <option value="business_administration">Business Administration</option>
                <option value="engineering">Engineering</option>
                <option value="arts">Arts</option>
            </select>
            <span class="error"><?php echo $errors['course'] ?? ''; ?></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <span class="error"><?php echo $errors['password'] ?? ''; ?></span>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword">
            <span class="error"><?php echo $errors['confirmPassword'] ?? ''; ?></span>

            <label>
                <input type="checkbox" id="terms" name="terms" required> I agree to the terms and conditions
            </label>
            <span class="error"><?php echo $errors['terms'] ?? ''; ?></span>

            <button type="submit" onclick="document.getElementById('form-action').value='create';">Register</button>
            <button type="submit" onclick="document.getElementById('form-action').value='update';">Update</button>
            <button type="submit" onclick="document.getElementById('form-action').value='delete';">Delete</button>
        </form>
    </div>
</body>
</html>

