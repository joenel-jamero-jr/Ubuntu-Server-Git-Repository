<!DOCTYPE html>
<html>

<head>
    <title>LFP Edit Data</title>
    <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/tout.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: Monaco, monospace;">

    <div class="navbar">
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
        <h1>Home > User Info > Edit</h1>
    </div>
    
    <div class="form-container" style="margin-top: 5%;">
        <?php
        // Include your database configuration
        include('config.php');

        // Retrieve the ID passed from dash.php
        $id = $_GET['id'];

        // Check if the "Edit" button is clicked
        if (isset($_POST['edit'])) {
            $isEditing = true;
        } else {
            $isEditing = false;
        }

        // Check if the "Save" button is clicked
        if (isset($_POST['save'])) {
            // Get updated values from the form
            $name = $_POST['name'];
            $plateNumber = $_POST['plateNumber'];
            $vehicleType = $_POST['vehicleType'];
            $contactNumber = $_POST['contactNumber'];
            $slot = $_POST['slot'];
            $slotNumber = $_POST['slotNumber'];
            $slotCode = $slot . $slotNumber; // Assuming Slot_Code is derived from Slot and SlotNumber
        
            // Update the user's data in the database
            $updateQuery = "UPDATE users SET Name = '$name', Plate_Num = '$plateNumber', Vehicle_Type = '$vehicleType', Contact_Number = '$contactNumber', Slot = '$slot', SlotNumber = '$slotNumber', Slot_Code = '$slotCode' WHERE id = $id";
            $mysqli->query($updateQuery);
            $isEditing = false;

            // Show SweetAlert after editing
            echo "<script>
                    Swal.fire({
                        title: 'Edit Successful!',
                        text: 'Your changes have been saved.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const urlParams = new URLSearchParams(window.location.search);
                        const aid = urlParams.get('aid'); // Get the aid from the current URL
                        window.location.href = 'dashb.php?aid=' + aid;
                    });
                </script>";
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
                    <input type="text" id="name" name="name" placeholder="Name" value="<?php echo $row['Name']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="contactNumber"></label>
                    <input type="text" id="contactNumber" name="contactNumber" value="<?php echo $row['Contact_Number']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="plateNumber"></label>
                    <input type="text" id="plateNumber" name="plateNumber" placeholder="Plate Number" value="<?php echo $row['Plate_Num']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="vehicleType"></label>
                    <input type="text" id="vehicleType" name="vehicleType" placeholder="Vehicle Type" value="<?php echo $row['Vehicle_Type']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="slot"></label>
                    <input type="text" id="slot" name="slot" placeholder="Slot" value="<?php echo $row['Slot']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="slotNumber"></label>
                    <input type="text" id="slotNumber" name="slotNumber" value="<?php echo $row['SlotNumber']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>><br>
                </div>

                <div class="input-container">
                    <label for="date"></label>
                    <input type="text" id="date" name="date" placeholder="Date" value="<?php echo $row['Date']; ?>" readonly><br>
                </div>

                <div class="input-container">
                    <label for="timeIn">Time In:</label>
                    <input type="text" id="timeIn" name="timeIn" placeholder="Time in" value="<?php echo $row['TimeIn']; ?>" readonly><br>
                </div>
                </div>
                
                <!-- Slot code column -->
                <div class="slot-code-column">
                    <h1 style="color: white;">Slot Code:</h1>
                    <!-- Slot code container -->
                    <div class="slot-code-container">
                        <span id="slotCode"><?php echo $row['Slot_Code']; ?></span>
                    </div>
                </div>
            </div>
            <br>
                <?php if ($isEditing) { ?>
                        <input type="submit" name="cancel" value="Cancel" style="background-color: red; color:white; border: 1px solid red; padding: 5px 25px; font-weight:bold;">
                        <input type="submit" name="save" value="Save" style="margin-left: 35%;">
                <?php } else { ?>
                    <a href="dashb.php?id=<?php echo $id; ?>&aid=<?php echo $_GET['aid']; ?>" class="back-button">Cancel</a>
                        <input type="submit" name="edit" value="Edit" style="margin-left: 35%;">
                <?php } ?>
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

</html>
