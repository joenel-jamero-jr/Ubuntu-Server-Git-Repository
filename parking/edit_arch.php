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
        <h1>Parking Logs > User Info > Edit</h1>
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
            $inBy = $_POST['inBy'];
            $outBy = $_POST['outBy'];

            // Update the data in the archives table
            $updateQuery = "UPDATE archives SET Name = '$name', Plate_Num = '$plateNumber', Vehicle_Type = '$vehicleType', Contact_Number = '$contactNumber', Slot = '$slot', SlotNumber = '$slotNumber', Slot_Code = '$slotCode', In_By = '$inBy', Out_By = '$outBy' WHERE id = $id";
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
                        window.location.href = 'archives.php?aid=' + aid;
                    });
                </script>";
        }

        // Query to retrieve data based on the ID
        $query = "SELECT * FROM archives WHERE id = $id";
        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        ?>

<form id="parkingForm" method="post">
    <div class="form-columns">
        <div class="input-column">
            <div class="input-container">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $row['Name']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="contactNumber">Contact Number:</label>
                <input type="text" id="contactNumber" name="contactNumber" value="<?php echo $row['Contact_Number']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="plateNumber">Plate Number:</label>
                <input type="text" id="plateNumber" name="plateNumber" value="<?php echo $row['Plate_Num']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="vehicleType">Vehicle Type:</label>
                <input type="text" id="vehicleType" name="vehicleType" value="<?php echo $row['Vehicle_Type']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="slot">Slot Name:</label>
                <input type="text" id="slot" name="slot" value="<?php echo $row['Slot']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="slotNumber">Slot Number:</label>
                <input type="text" id="slotNumber" name="slotNumber" value="<?php echo $row['SlotNumber']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="date">Date:</label>
                <input type="text" id="date" name="date" value="<?php echo $row['Date']; ?>" readonly>
            </div>

            <div class="input-container">
                <label for="timeIn">Time In:</label>
                <input type="text" id="timeIn" name="timeIn" value="<?php echo $row['TimeIn']; ?>" readonly>
            </div>

            <div class="input-container">
                <label for="timeOut">Time Out:</label>
                <input type="text" id="timeOut" name="timeOut" value="<?php echo $row['TimeOut']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="inBy">In By:</label>
                <input type="text" id="inBy" name="inBy" value="<?php echo $row['In_By']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>

            <div class="input-container">
                <label for="outBy">Out By:</label>
                <input type="text" id="outBy" name="outBy" value="<?php echo $row['Out_By']; ?>" <?php if (!$isEditing) echo 'readonly'; ?>>
            </div>
            <br>
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
    <?php if ($isEditing) { ?>
        <input type="submit" name="cancel" value="Cancel" style="background-color: red; color:white;">
        <input type="submit" name="save" value="Save" style="margin-left: 31%;">
    <?php } else { ?>
        <a href="archives.php?id=<?php echo $id; ?>&aid=<?php echo $_GET['aid']; ?>" class="back-button">Cancel</a>
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

</body>

</html>
