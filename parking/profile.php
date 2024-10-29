<?php
    // Include your database configuration
    require_once('config.php');

    // Check if aid is provided in the URL
    if (isset($_GET['aid'])) {
        // Get the aid value from the URL
        $aid = $_GET['aid'];

        // Initialize variables to store user data
        $firstname = $lastname = $contact_number = $username = $pic = "";

        // Check if form is submitted for updating user data
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve form data
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $contact_number = $_POST["contact_number"];
            $username = $_POST["username"];
            $new_password = $_POST["new_password"];
            $repeat_new_password = $_POST["repeat_new_password"];

            // Check if a new profile picture is uploaded
            if(isset($_FILES['new_profile_picture']) && $_FILES['new_profile_picture']['error'] === UPLOAD_ERR_OK) {
                // Process the uploaded profile picture
                $file_tmp = $_FILES['new_profile_picture']['tmp_name'];
                $file_name = $_FILES['new_profile_picture']['name'];
                $file_size = $_FILES['new_profile_picture']['size'];
                $file_type = $_FILES['new_profile_picture']['type'];

                // Check file type
                $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
                if (in_array($file_type, $allowed_types)) {
                    // Move the uploaded file to a permanent location
                    $upload_path = 'uploads/';
                    $new_file_name = $upload_path . $file_name;
                    move_uploaded_file($file_tmp, $new_file_name);

                    // Update the pic field in the database
                    $update_pic_query = "UPDATE admin SET pic=? WHERE aid=?";
                    $stmt = $mysqli->prepare($update_pic_query);
                    if ($stmt) {
                        $stmt->bind_param('si', $new_file_name, $aid);
                        if ($stmt->execute()) {
                            // Update successful
                            $pic = $new_file_name; // Update the $pic variable with the new file path
                        } else {
                            // Handle update failure
                            echo "Error updating profile picture: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        // Handle SQL error
                        echo "Error: " . $mysqli->error;
                    }
                } else {
                    echo "Invalid file type. Only JPEG, PNG, and GIF files are allowed.";
                }
            }

            // Update the admin table with the new data (excluding pic)
            $update_query = "UPDATE admin SET firstname=?, lastname=?, contact_number=?, username=?";
            $update_params = array($firstname, $lastname, $contact_number, $username);

            // Check if new passwords are provided and match
            if (!empty($new_password) && $new_password === $repeat_new_password) {
                $update_query .= ", password=?";
                $update_params[] = $new_password; // Store the password directly without hashing
            }

            $update_query .= " WHERE aid=?";
            $update_params[] = $aid;

            // Prepare and execute the update query
            $stmt = $mysqli->prepare($update_query);
            if ($stmt) {
                $stmt->bind_param(str_repeat('s', count($update_params)), ...$update_params);
                if ($stmt->execute()) {
                    // Update successful
                } else {
                    // Handle update failure
                    echo "Error updating user data: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Handle SQL error
                echo "Error: " . $mysqli->error;
            }
        } else {
            // Query to retrieve user's data from the admin table using the aid
            $query = "SELECT * FROM admin WHERE aid = $aid";
            $result = $mysqli->query($query);

            // Check if the query was successful and data was retrieved
            if ($result && $result->num_rows > 0) {
                // Fetch user's data
                $row = $result->fetch_assoc();

                // Assign fetched data to variables
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $contact_number = $row['contact_number'];
                $username = $row['username'];
                $pic = $row['pic']; // Fetch the profile picture path

                // Close the result set
                $result->close();
            } else {
                // Redirect to a page with an error message if no data found for the provided aid
                header("Location: error.php?message=No data found for the provided aid");
                exit();
            }
        }
    } else {
        // Redirect to a page with an error message if aid is not provided in the URL
        header("Location: error.php?message=No aid provided");
        exit();
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>LFP Edit Profile</title>
    <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: Monaco, monospace;" class="bg">
    <div class="navbar">
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
        <h1>Home > Profile > Edit</h1>
    </div>

    <div class="reg-container">
        <div class="registration-form">
            <div class="left-column">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?aid=<?php echo $aid; ?>" method="post"
                    enctype="multipart/form-data">
                    <label for="firstname">Firstname:</label><br>
                    <input type="text" id="firstname" name="firstname" required placeholder="First Name"
                        value="<?php echo $firstname; ?>"><br><br>
                    <label for="lastname">Last Name:</label><br>
                    <input type="text" id="lastname" name="lastname" required placeholder="Last Name"
                        value="<?php echo $lastname; ?>"><br><br>
                    <label for="contact_number">Contact Number:</label><br>
                    <input type="tel" id="contact_number" name="contact_number" required placeholder="Contact Number"
                        value="<?php echo $contact_number; ?>"><br><br>
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required placeholder="Username"
                        value="<?php echo $username; ?>"><br><br>
                    <label for="new_password">New Password:</label><br>
                    <input type="password" id="new_password" name="new_password" placeholder="New Password"><br><br>                   
                    <label for="repeat_new_password">Repeat New Password:</label><br>
                    <input type="password" id="repeat_new_password" name="repeat_new_password"
                        placeholder="Repeat New Password"><br><br>
                    <input type="button" value="Cancel"
                        onclick="window.location.href='dashb.php?aid=<?php echo $aid; ?>'" class="canc-btn"
                        style="margin-bottom: 1%;">
            </div>
            <div class="right-column">
                <!-- Current profile picture -->
                <?php if (!empty($pic)): ?>
                <img id="current_profile_picture" src="<?php echo $pic; ?>" alt="Profile Picture" style="width: 200px;">
                <?php else: ?>
                <p>No profile picture available</p>
                <?php endif; ?>
                <!-- Button to choose a new profile picture -->
                <label for="new_profile_picture">Choose a new profile picture:</label><br>
                    <input type="file" id="new_profile_picture" name="new_profile_picture"
                        onchange="previewImage(event)"><br><br>
                <!-- Preview of the new profile picture -->
                <img id="profile_picture_preview" alt="Preview" style="width: 200px; margin-top: 10px;">
                    <!-- Submit button -->
                    <input type="submit" value="Save" class="reg-btn" style="margin-top: 20px;">

            </div>
        </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('profile_picture_preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

<?php
    // Display SweetAlert on successful form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if new passwords are provided and match
        if (!empty($new_password) && $new_password !== $repeat_new_password) {
            echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: 'Passwords do not match!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        title: 'Success',
                        text: 'Profile updated successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'dashb.php?aid=" . $aid . "';
                        }
                    });
                  </script>";
        }
    }
?>


</body>
</html>