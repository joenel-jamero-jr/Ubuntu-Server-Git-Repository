<!DOCTYPE html>
<html>
    <head>
    <title>LFP Time Out User</title>
        <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/tout.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

<body style="font-family: Monaco, monospace;">

<div class="navbar">
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
        <h1>Home > User Info > Time Out</h1>
    </div>

        <div class="form-container" style="margin-top: 5%;">
<?php
// Include your database configuration
include('config.php');

// Retrieve the ID passed from dash.php
$id = $_GET['id'];

// Get the admin ID (aid) from the URL
$aid = $_GET['aid'];

// Check if the "Time Out" button is clicked
if (isset($_POST['TimeOut'])) {
    // Get data from the form
    $name = $_POST['name'];
    $contactNumber = $_POST['contactNumber']; // Add this line to get contact number
    $plateNumber = $_POST['plateNumber'];
    $vehicleType = $_POST['vehicleType'];
    $slot = $_POST['slot'];
    $slotNumber = $_POST['slotNumber']; // Add this line to get slot number
    $date = $_POST['date'];
    $timeIn = $_POST['timeIn'];
    $timeOut = $_POST['timeOut'];
    $slotCode = $_POST['slotCode']; // Add this line to get slot code

    // Get the first name and last name of the administrator who clicked the "Time Out"
    $adminQuery = "SELECT firstname, lastname FROM admin WHERE aid = ?";
    $stmt = $mysqli->prepare($adminQuery);

    if ($stmt) {
        // Bind the aid parameter
        $stmt->bind_param("i", $aid);

        // Execute the statement
        $stmt->execute();

        // Bind the result variables
        $stmt->bind_result($firstName, $lastName);

        // Fetch the result
        $stmt->fetch();

        // Close the statement
        $stmt->close();

        // Concatenate first name and last name
        $outBy = $firstName . ' ' . $lastName;

        // Insert the data into the "archives" table
        $insertQuery = "INSERT INTO archives (Name, Contact_Number, Plate_Num, Vehicle_Type, Slot, SlotNumber, Slot_Code, Date, TimeIn, TimeOut, In_By, Out_By)
                        VALUES ('$name', '$contactNumber', '$plateNumber', '$vehicleType', '$slot', '$slotNumber', '$slotCode', '$date', '$timeIn', '$timeOut', (SELECT In_By FROM users WHERE id = $id), '$outBy')";

        // Perform the query
        if ($mysqli->query($insertQuery) === TRUE) {
            // Time out recorded successfully
            // Delete the data from the "users" table
            $deleteQuery = "DELETE FROM users WHERE id = $id";
            if ($mysqli->query($deleteQuery) === TRUE) {
                // Display a SweetAlert
                echo "<script>
                Swal.fire({
                    title: 'Time Out Recorded!',
                    text: 'The time out has been successfully recorded.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to dashboard with the aid parameter preserved
                    window.location.href = 'dashb.php?aid=$aid';
                });
                </script>";
                $mysqli->close();
                exit(); // Stop further execution of PHP code
            } else {
                // Error handling if deletion fails
                echo "Error deleting data: " . $mysqli->error;
            }
        } else {
            // Error handling if insertion fails
            echo "Error inserting data: " . $mysqli->error;
        }
    } else {
        // Error handling if prepare fails
        echo "Error preparing statement: " . $mysqli->error;
    }
}

// Query to retrieve data based on the ID
$query = "SELECT * FROM users WHERE id = $id";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>

<form id="parkingForm" method="post">
            <div class="form-columns">
                <div class="input-column">
        <div class="input-container">
        <label for="name"></label>
        <input type="text" id="name" name="name" placeholder="Name" value="<?php echo $row['Name']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="plateNumber"></label>
        <input type="text" id="plateNumber" name="contactNumber" placeholder="Contact Number" value="<?php echo $row['Contact_Number']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="plateNumber"></label>
        <input type="text" id="plateNumber" name="plateNumber" placeholder="Plate Number" value="<?php echo $row['Plate_Num']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="vehicleType"></label>
        <input type="text" id="vehicleType" name="vehicleType" placeholder="Vehicle Type" value="<?php echo $row['Vehicle_Type']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="slot"></label>
        <input type="text" id="slot" name="slot" placeholder="Slot" value="<?php echo $row['Slot']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="slot"></label>
        <input type="text" id="slot" name="slotNumber" placeholder="Slot" value="<?php echo $row['SlotNumber']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="date"></label>
        <input type="text" id="date" name="date" placeholder="Date" value="<?php echo $row['Date']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="timeIn">Time In:</label>
        <input type="text" id="timeIn" name="timeIn" placeholder="Time in" value="<?php echo $row['TimeIn']; ?>" readonly><br>
    </div>

    <div class="input-container">
        <label for="timeOut">Time out:</label><br>
        <input type="text" id="timeOut" name="timeOut" readonly>
    </div>
    <br>
    <a href="dashb.php?aid=<?php echo $_GET['aid']; ?>" class="back-button">Cancel</a>
    <input type="submit" name="TimeOut" value="Time Out" style="margin-left: 48%;">
    </div>
                <!-- Slot code column -->
                <div class="slot-code-column">
                    <h1 style="color: white;">Slot Code:</h1>
                    <!-- Slot code container -->
                    <div class="slot-code-container">
                    <input type="hidden" id="slotCode" name="slotCode" value="<?php echo $row['Slot_Code']; ?>">
                        <span id="slotCode"><?php echo $row['Slot_Code']; ?></span>
                    </div>
                </div>
</form>
<?php
} else {
    echo "No data found for the specified ID.";
}
$mysqli->close();
?>
</div>


<script>

// JavaScript to handle label placeholders
const inputs = document.querySelectorAll('.input-container input');


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
    const timeInput = document.getElementById('timeOut');
    // Set initial values for date and time fields when the page loads
    timeInput.value = getCurrentTime();

    
</script>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>