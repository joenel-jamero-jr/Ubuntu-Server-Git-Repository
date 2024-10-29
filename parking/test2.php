<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/parkhere.css">
    </head>

<body style="font-family: Monaco, monospace;">

<div class="navbar">
    <marquee behavior="loop" direction="right"><h1>LFP: Looking for Parking</h1></marquee>
    <a href="park.php" class="back-button">Back</a>
</div>
<br>

<div class="logo-container">
    <img src="css/pics/download.png" alt="USTP Logo" class="logo">
</div>

<div class="form-container">
    <form id="parkingForm" method="POST">
        <div class="input-container">
            <label for="name"></label>
            <input type="text" id="name" name="name" required data-placeholder="Name" placeholder="Name"><br><br>
        </div>

        <div class="input-container">
            <label for="plateNumber"></label>
            <input type="text" id="plateNumber" name="plateNumber" required data-placeholder="Plate Number" placeholder="Plate Number"><br><br>
        </div>

        <div class="input-container">
            <label for="vehicleType"></label>
            <select id="vehicleType" name="vehicleType" required>
                <option value="">Vehicle Type:</option>
                <option value="Car">Car</option>
                <option value="Motorcycle">Motorcycle</option>
            </select><br><br>
        </div>

        <div class="input-container">
            <label for="slot"></label>
            <input type="text" id="slot" name="slot" readonly placeholder="Slot">
        </div>

        <div class="input-container">
            <label for="date">Date:</label>
            <input type="text" id="date" name="date" readonly placeholder="Date"><br>
        </div>

        <div class="input-container">
            <label for="timeIn">Time In:</label>
            <input type="text" id="timeIn" name="timeIn" readonly placeholder="Time in"><br><br>
        </div>
        <center>
        <input type="submit" value="Park">
        </center>
    </form>
</div>

<?php
// Include your database configuration
include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $name = $_POST['name'];
    $plateNumber = $_POST['plateNumber'];
    $vehicleType = $_POST['vehicleType'];
    $slot = $_POST['slot'];
    $date = $_POST['date'];
    $timeIn = $_POST['timeIn'];

    // Check if the input is invalid
    if (($slot === 'Slot A' && $vehicleType === 'Motorcycle') || ($slot === 'Slot B' && $vehicleType === 'Car')) {
        echo '<script>alert("Invalid parking input");</script>';
    } else {
        // You can also add validation and sanitization here to improve security

        // Create an SQL query to insert the data into the users table
        $insertQuery = "INSERT INTO users (Name, Plate_Num, Vehicle_Type, Slot, Date, TimeIn) VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $mysqli->prepare($insertQuery);

        // Bind parameters to the statement
        $stmt->bind_param("ssssss", $name, $plateNumber, $vehicleType, $slot, $date, $timeIn);

        // Execute the statement
        if ($stmt->execute()) {
            // Data has been inserted successfully

            // Set a session variable to indicate successful registration
            session_start();
            $_SESSION['registration_success'] = true;

            // Close the statement and the database connection
            $stmt->close();
            $mysqli->close();

            // Alert for registration success
            echo '<script>';
            echo 'alert("Registration successful");';
            echo '</script>';

            // Redirect to index.php after a short delay (adjust as needed)
            echo '<script>';
            echo 'setTimeout(function() {';
            echo '  window.location.href = "park.php";';
            echo '}, 200);'; // Adjust the delay as needed
            echo '</script>';
        } else {
            // Something went wrong
            $message = "Error: " . $mysqli->error;
            echo '<script>';
            echo 'alert("' . $message . '");';
            echo '</script>';
        }
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // JavaScript to handle setting the slot input value
    const slotInput = document.getElementById('slot');
    const urlParams = new URLSearchParams(window.location.search);
    const slot = urlParams.get('slot');

    if (slot) {
        slotInput.value = `Slot ${slot}`;
    }

    // JavaScript to handle label placeholders
    const inputs = document.querySelectorAll('.input-container input');

    inputs.forEach(input => {
        const label = input.previousElementSibling;
        const placeholderText = input.getAttribute('data-placeholder');

        input.addEventListener('focus', () => {
            label.style.display = 'none';
        });

        input.addEventListener('blur', () => {
            if (input.value === '') {
                label.style.display = 'block';
            }
        });
    });

    // Function to format the current date as MM:DD:YYYY
    function getCurrentDate() {
        const currentDate = new Date();
        const month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        const day = currentDate.getDate().toString().padStart(2, '0');
        const year = currentDate.getFullYear();
        return `${month}/${day}/${year}`;
    }

    // Function to format the current time as HH:MM AM/PM
    function getCurrentTime() {
        const currentDate = new Date();
        const hours = currentDate.getHours();
        const minutes = currentDate.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = (hours % 12 || 12).toString().padStart(2, '0');
        const formattedMinutes = minutes.toString().padStart(2, '0');
        return `${formattedHours}:${formattedMinutes} ${ampm}`;
    }

    // Get references to form fields
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('timeIn');

    // Set initial values for date and time fields when the page loads
    dateInput.value = getCurrentDate();
    timeInput.value = getCurrentTime();

// Function to display a SweetAlert with a message and then redirect
function showAlertAndRedirect() {
        // Get data from the form
        const name = document.getElementById('name').value;
        const plateNumber = document.getElementById('plateNumber').value;
        const vehicleType = document.getElementById('vehicleType').value;
        const slot = document.getElementById('slot').value;
        const date = document.getElementById('date').value;
        const timeIn = document.getElementById('timeIn').value;

        // Create a message with the input data
        const message = "Data has been successfully registered.\n\n" +
            "Name: " + name + "\n" +
            "Plate Number: " + plateNumber + "\n" +
            "Vehicle Type: " + vehicleType + "\n" +
            "Slot: " + slot + "\n" +
            "Date: " + date + "\n" +
            "Time In: " + timeIn;

        // Display a SweetAlert with the message
        Swal.fire({
            title: 'Registration Successful!',
            text: message,
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Use setTimeout to delay the redirection by a few milliseconds
            setTimeout(function () {
                // Redirect to park.php
                window.location.href = "park.php";
            }, 200); // Adjust the delay as needed
        });
    }
</script>

</body>
</html>