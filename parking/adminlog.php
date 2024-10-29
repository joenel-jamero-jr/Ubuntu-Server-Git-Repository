<!DOCTYPE html>
<html>
<head>
    <title>LFP Administrator Login</title>
    <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/adminlog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body style="font-family: Monaco, monospace;" class="bg">
    <div class="logo-container">
        <img src="css/pics/download.png" alt="USTP Logo" class="logo">
    </div>

    <div class="login-container">
        <div class="left">
            <h1 style="font-size: 45px;">ADMIN LOGIN</h1>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="input-container">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="username" required>
                </div>
                <div class="input-container">
                    <label for="password">Password:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="password" required>
                        <i class="fa fa-eye-slash password-toggle" aria-hidden="true" onclick="togglePasswordVisibility()"></i>
                    </div>
                </div>
                <div class="login-button">
                    <button type="submit" class="login-btn">Login</button>
                </div>
                <div class="reg-link">
                    <p>Not yet Registered? <a href="reg.php">Register Here!</a></p>
                </div>
            </form>
        </div>
        <div class="right">
            <a href="index.php" i class="fa fa-close" style="font-size:30px; text-decoration:none; color:grey; margin-left: 93%;"></a>
            <div class="image-container">
                <img src="css/pics/lgpnobg.png" alt="LFP-logo" class="second-logo">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // JavaScript code for displaying SweetAlert2 messages
        document.addEventListener('DOMContentLoaded', function () {
            <?php
            session_start();
            require_once('config.php');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Retrieve username and password from the form
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Check if the provided credentials exist in the admin table
                $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
                $result = $mysqli->query($query);

                if ($result && $result->num_rows > 0) {
                    // Fetch the user data
                    $row = $result->fetch_assoc();
                    // Set session variable for successful login
                    $_SESSION['login_success'] = true;
                    // Redirect to dashb.php and pass the ID as a query parameter
                    $aid = $row['aid'];

                    // Display a success message before redirection
                    echo 'Swal.fire({';
                    echo '  title: "Login Successful",';
                    echo '  text: "You have successfully logged in.",';
                    echo '  icon: "success",';
                    echo '  confirmButtonText: "OK"';
                    echo '}).then((result) => {';
                    echo '  if (result.isConfirmed) {';
                    echo "    window.location.href = 'dashb.php?aid=$aid';";
                    echo '  }';
                    echo '});';
                } else {
                    // Display an error message if the username or password is incorrect
                    echo 'Swal.fire({';
                    echo '  title: "Invalid Credentials",';
                    echo '  text: "Please enter a valid username and password.",';
                    echo '  icon: "error",';
                    echo '  confirmButtonText: "OK"';
                    echo '});';
                }

                // Close the database connection
                $mysqli->close();
            }
            ?>
        });

        // JavaScript function to toggle password visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const passwordToggle = document.querySelector('.password-toggle');

            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            passwordToggle.classList.toggle('fa-eye-slash');
            passwordToggle.classList.toggle('fa-eye');
        }
    </script>
</body>
</html>
