<?php

$errors = array();
$formErr = "";
$successMsg = "";

if (isset($_POST['formSubmit'])) {
    // Validate fullname
    if (empty($_POST['fullname'])) {
        $errors['nameErr'] = "Name is required";
    }

    // Validate email
    if (empty($_POST['email'])) {
        $errors['emailErr'] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['emailErr'] = "Invalid email format";
    }

    // Validate password
    if (empty($_POST['password'])) {
        $errors['passwordErr'] = "Password is required";
    } elseif (strlen($_POST['password']) < 8) {
        $errors['passwordErr'] = "Password must be at least 8 characters";
    }

    // Validate confirm password
    if (empty($_POST['confirm-pass'])) {
        $errors['confirmPassErr'] = "Confirmation is required";
    } elseif ($_POST['confirm-pass'] != $_POST['password']) {
        $errors['confirmPassErr'] = "Passwords do not match";
    }

    // Check if profile picture was uploaded
    if (isset($_FILES["profile-pic"]) && $_FILES["profile-pic"]["error"] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $filename = basename($_FILES["profile-pic"]["name"]);
        $target_file = $target_dir . uniqid() . "-" . $filename; 
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Debug information
        error_log("File size: " . $_FILES["profile-pic"]["size"]);
        error_log("File type: " . $imageFileType);
        error_log("Target file: " . $target_file);

        // Check file size (2MB limit)
        if ($_FILES["profile-pic"]["size"] > 2 * 1024 * 1024) {
            $errors['profilePicErr'] = "Sorry, your file is too large";
        } 
        // Check file type
        elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $errors['profilePicErr'] = "Only JPG, JPEG, & PNG files are allowed";
        } 
        // Try to upload
        else {
            if (!move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $target_file)) {
                $errors['profilePicErr'] = "Upload failed: " . error_get_last()['message'];
            }
        }
    } else {
        $errors['profilePicErr'] = "Picture is required";
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $errors['genderErr'] = "Gender is required";
    }

    // Validate terms and conditions
    if (!isset($_POST["toc"])) {
        $errors['tocErr'] = "You must accept the terms and conditions";
    }

    if (!empty($errors)) {
        // Build query string with errors
        $errorQuery = http_build_query($errors);
        // Add form values to preserve them
        $formData = http_build_query([
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email']
        ]);
        // Redirect back with errors and form data
        header("Location: register.html?" . $errorQuery . "&" . $formData);
        exit();
    }

    // If no errors, process the form
    $fullname = htmlspecialchars($_POST['fullname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');

    // Initialize registration.json if it doesn't exist
    if (!file_exists('registration.json')) {
        file_put_contents('registration.json', json_encode([]));
    }

    $data = file_get_contents('registration.json');
    $data_array = json_decode($data, true);
    if ($data_array === null) {
        $data_array = [];
    }

    $input = array(
        'fullname' => $fullname,
        'email' => $email,
        'password' => $password,
        'gender' => $gender,
        'profile_picture' => basename($target_file)
    );

    // Append the POST data
    $data_array[] = $input;
    
    // Save to JSON file
    file_put_contents('registration.json', json_encode($data_array, JSON_PRETTY_PRINT));

    // Start session and store user data
    session_start();
    $_SESSION['user'] = $input;
    $_SESSION['logged_in'] = true;

    // Redirect to welcome page
    header("Location: welcome.php");
    exit();
}

// If accessed directly without form submission
if (!isset($_POST['formSubmit'])) {
    header("Location: register.html");
    exit();
}
?>
