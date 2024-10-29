<?php
require_once('config.php');

// Define an array to store the response data
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $contact_number = $_POST['contact_number'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $admin_username = $_POST['admin_username']; // For security purposes

    // Check if passwords match
    if ($password !== $repeat_password) {
        $response['status'] = 'error';
        $response['message'] = 'Passwords do not match';
        echo json_encode($response); // Return JSON response
        exit(); // Stop execution if passwords don't match
    }

    // Check if admin username exists in the database
    $query = "SELECT * FROM admin WHERE username = '$admin_username'";
    $result = mysqli_query($mysqli, $query);

    if (mysqli_num_rows($result) == 0) {
        $response['status'] = 'error';
        $response['message'] = 'Admin username does not exist';
        echo json_encode($response); // Return JSON response
        exit(); // Stop execution if admin username doesn't exist
    }

    // Handle file upload for profile picture
    $target_dir = "uploads/"; // Directory where the file will be uploaded
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    
    // Check if a file was selected
    if (!empty($_FILES["profile_picture"]["tmp_name"])) {
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'File is not an image';
            echo json_encode($response);
            exit(); // Stop execution if file is not an image
        }

        // Check file size
        if ($_FILES["profile_picture"]["size"] > 5000000) {
            $response['status'] = 'error';
            $response['message'] = 'File is too large';
            echo json_encode($response);
            exit(); // Stop execution if file is too large
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid file format';
            echo json_encode($response);
            exit(); // Stop execution if file format is invalid
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $response['status'] = 'error';
            $response['message'] = 'Sorry, your file was not uploaded';
            echo json_encode($response);
            exit(); // Stop execution if file upload encountered an error
        } else {
            // Attempt to upload file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $pic_path = $target_file;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error uploading file';
                echo json_encode($response);
                exit(); // Stop execution if file upload encountered an error
            }
        }
    } else {
        // If no file was selected, use default.jpg
        $pic_path = "uploads/default.jpg";
    }

    // Insert user registration data into the database
    $insert_query = "INSERT INTO admin (firstname, lastname, contact_number, username, password, pic) 
                     VALUES ('$firstname', '$lastname', '$contact_number', '$username', '$password', '$pic_path')";

    if (mysqli_query($mysqli, $insert_query)) {
        $response['status'] = 'success';
        $response['message'] = 'User added successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . mysqli_error($mysqli);
    }

    // Close database connection
    mysqli_close($mysqli);

    // Send JSON response
    echo json_encode($response);
    exit(); // Stop execution after processing form data
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
    <link rel="stylesheet" href="css/reg.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="font-family: Monaco, monospace;" class="bg">

    <div class="logo-container">
        <img src="css/pics/download.png" alt="USTP Logo" class="logo">
    </div>

    <div class="reg-container">
    <h2 style="color: white;">Admin Registration</h2>
    <div class="registration-form">
        <div class="left-column">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="firstname"></label><br>
            <input type="text" id="firstname" name="firstname" required placeholder="First Name"><br>
            <label for="lastname"></label><br>
            <input type="text" id="lastname" name="lastname" required placeholder="Last Name"><br>
            <label for="contact_number"></label><br>
            <input type="tel" id="contact_number" name="contact_number" required placeholder="Contact Number"><br>
            <label for="username"></label><br>
            <input type="text" id="username" name="username" required placeholder="Username"><br>
            <label for="password"></label><br>
            <input type="password" id="password" name="password" required placeholder="Password"><br>
            <label for="repeat_password"></label><br>
            <input type="password" id="repeat_password" name="repeat_password" required placeholder="Repeat Password"><br>
            <label for="admin_username"></label><br>
            <input type="text" id="admin_username" name="admin_username" required placeholder="Another Admin's Username"><br>
            <br><br><br>
            <input type="button" value="Cancel" onclick="window.location.href='adminlog.php'" class="canc-btn" style="margin-top: 12px;">
        </div>
        <div class="right-column">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <!-- Input for profile picture -->
                <!-- Add an image tag with the default image and make it clickable -->
                <div id="imagePreview" class="image-preview">
                    <img src="uploads/default.jpg" id="profilePicture" alt="Default Image" style="width: 200px; cursor: pointer;">
                </div>
                <!-- Add a separate button for uploading a picture -->
                <label for="fileUpload" class="custom-file-upload">
                    <i class="fa fa-cloud-upload">Upload Picture</i>
                </label>
                <input type="file" id="fileUpload" name="profile_picture" style="display:none;" accept="image/*">
                <br><br><br>
                <input type="submit" value="Register" class="reg-btn"><br>
            </form>
        </div>
    </div>
    </div>

    <script>
    // Function to display uploaded image
    function previewImage(input) {
        var preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '200px'; // Adjust the size as needed
                preview.appendChild(img);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Event listener for file input change
    document.getElementById('fileUpload').addEventListener('change', function () {
        previewImage(this);
    });

    // Function to handle click on the default image
    document.getElementById('profilePicture').addEventListener('click', function () {
        document.getElementById('fileUpload').click(); // Trigger click on file input
    });

    // Submit form and handle response
    document.querySelector('form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission
        var formData = new FormData(this);

        fetch(this.action, {
            method: this.method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message using SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Success',
                    text: data.message,
                }).then((result) => {
                    // Redirect to admin login page after the alert is closed
                    if (result.isConfirmed || result.isDismissed) {
                        window.location.href = 'adminlog.php'; // Redirect to admin login page
                    }
                });
            } else {
                // Show error message using SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>

</body>
</html>