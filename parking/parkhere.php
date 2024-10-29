<!DOCTYPE html>
<html>
<head>
<title>LFP Park Here</title>
<link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/parkhere.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: Monaco, monospace;">

<div class="navbar">
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
        <h1>Home > Park Here</h1>
    </div>

<div class="form-container" style="margin-top: 5%;">
    <form id="parkingForm" method="POST">
        <div class="form-columns">
            <div class="input-column">
                <div class="input-container">
                    <label for="name"></label>
                    <input type="text" id="name" name="name" required data-placeholder="Name" placeholder="Fullname"><br>
                </div>

                <div class="input-container">
                    <label for="contactNumber"></label>
                    <input type="text" id="contactNumber" name="contactNumber" required data-placeholder="Contact Number" placeholder="Contact Number"><br>
                </div>

                <div class="input-container">
                    <label for="plateNumber"></label>
                    <input type="text" id="plateNumber" name="plateNumber" required data-placeholder="Plate Number" placeholder="Plate Number"><br>
                </div>

                <div style="margin-top: 4px; margin-bottom: 10px;" class="input-container">
                    <label for="vehicleType"></label>
                    <select id="vehicleType" name="vehicleType" required>
                        <option style="background-color:white;" value="">Vehicle Type:</option>
                        <option style="background-color:white;" value="Car">Car</option>
                        <option style="background-color:white;" value="Motorcycle">Motorcycle</option>
                    </select><br>
                </div>

                <div class="input-container">
                    <label for="slot"></label>
                    <input type="text" id="slot" name="slot" readonly placeholder="Slot"><br>
                </div>

                <div class="input-container">
                    <label for="slotNumber"></label>
                    <input type="text" id="slotNumber" name="slotNumber" readonly placeholder="Slot Number"><br>
                </div>

                <div class="input-container">
                    <label for="date"></label>
                    <input type="text" id="date" name="date" readonly placeholder="Date"><br>
                </div>

                <div class="input-container">
                    <label for="timeIn">Time In:</label>
                    <input type="text" id="timeIn" name="timeIn" readonly placeholder="Time in"><br><br>
                </div>
            </div>

            <!-- Slot code column -->
            <div class="slot-code-column">
                <h1 style="color: white;">Slot Code:</h1>
                <!-- Slot code container -->
                <div class="slot-code-container">
                    <span id="slotCode"></span>
                </div>
            </div>
        </div>
            <a href="dashb.php?aid=<?php echo isset($_GET['aid']) ? $_GET['aid'] : ''; ?>" class="back-button">Cancel</a>
            <input type="submit" value="Park" style="margin-left: 32%;">
    </form>
</div>

<?php
// Include your database configuration
include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $name = $_POST['name'];
    $contactNumber = $_POST['contactNumber'];
    $plateNumber = $_POST['plateNumber'];
    $vehicleType = $_POST['vehicleType'];
    $slot = $_POST['slot'];
    $slotNumber = $_POST['slotNumber'];
    $date = $_POST['date'];
    $timeIn = $_POST['timeIn'];
    $slotCode = $slot . $slotNumber; // Concatenate slot and slotNumber to form the slot code

    // Get the admin ID (aid) from the URL
    $aid = isset($_GET['aid']) ? $_GET['aid'] : '';

    // Query to fetch the administrator's data based on the provided aid
    $adminQuery = "SELECT CONCAT(firstname, ' ', lastname) AS in_by FROM admin WHERE aid = ?";
    $stmt = $mysqli->prepare($adminQuery);

    if ($stmt) {
        // Bind the aid parameter
        $stmt->bind_param("i", $aid);

        // Execute the statement
        $stmt->execute();

        // Bind the result variables
        $stmt->bind_result($inBy);

        // Fetch the result
        $stmt->fetch();

        // Close the statement
        $stmt->close();

        // Insert data into the users table
        $insertQuery = "INSERT INTO users (Name, Contact_Number, Plate_Num, Vehicle_Type, Slot, SlotNumber, Slot_Code, Date, TimeIn, In_By) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);

        if ($stmt) {
            // Bind parameters to the statement
            $stmt->bind_param("ssssssssss", $name, $contactNumber, $plateNumber, $vehicleType, $slot, $slotNumber, $slotCode, $date, $timeIn, $inBy);

            // Execute the statement
            if ($stmt->execute()) {
                // Data has been inserted successfully
                // Set a session variable to indicate successful registration
                session_start();
                $_SESSION['registration_success'] = true;

                // Close the statement and the database connection
                $stmt->close();
                $mysqli->close();

                // Display a SweetAlert for successful registration and redirect
                echo '<script>';
                echo 'Swal.fire({';
                echo '  title: "Registration Successful!",';
                echo '  text: "Data has been successfully registered.",';
                echo '  icon: "success",';
                echo '}).then(() => {';
                echo '  const urlParams = new URLSearchParams(window.location.search);';
                echo '  const aid = urlParams.get("aid");'; // Get the aid from the current URL
                echo '  window.location.href = "dashb.php?aid=" + aid;'; // Redirect to dashb.php with aid
                echo '});';
                echo '</script>';
            } else {
                // Handle execution error
                $error_message = "Error executing SQL query: " . $stmt->error;
                echo '<script>alert("' . $error_message . '");</script>';
            }
        } else {
            // Handle prepare error
            $error_message = "Error preparing SQL statement: " . $mysqli->error;
            echo '<script>alert("' . $error_message . '");</script>';
        }
    } else {
        // Handle prepare error
        $error_message = "Error preparing SQL statement: " . $mysqli->error;
        echo '<script>alert("' . $error_message . '");</script>';
    }
} else {
    // Handle form submission method error
}
?>

<script>
    // JavaScript to handle setting the slot input value
    const slotInput = document.getElementById('slot');
    const slotNumberInput = document.getElementById('slotNumber');
    const slotCodeSpan = document.getElementById('slotCode');
    const urlParams = new URLSearchParams(window.location.search);
    const slot = urlParams.get('slot');
    const slotNumber = urlParams.get('slotNumber');

    if (slot) {
        slotInput.value = slot;
    }

    if (slotNumber) {
        slotNumberInput.value = slotNumber;
    }

    // Set slot code
    if (slot && slotNumber) {
        slotCodeSpan.textContent = slot + slotNumber;
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
        const contactNumber = document.getElementById('contactNumber').value;
        const plateNumber = document.getElementById('plateNumber').value;
        const vehicleType = document.getElementById('vehicleType').value;
        const slot = document.getElementById('slot').value;
        const slotNumber = document.getElementById('slotNumber').value;
        const date = document.getElementById('date').value;
        const timeIn = document.getElementById('timeIn').value;

        // Create a message with the input data
        const message = "Data has been successfully registered.\n\n" +
            "Name: " + name + "\n" +
            "Contact Number: " + contactNumber + "\n" +
            "Plate Number: " + plateNumber + "\n" +
            "Vehicle Type: " + vehicleType + "\n" +
            "Slot: " + slot + "\n" +
            "Slot Number: " + slotNumber + "\n" +
            "Date: " + date + "\n" +
            "Time In: " + timeIn;

    }
</script>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>
