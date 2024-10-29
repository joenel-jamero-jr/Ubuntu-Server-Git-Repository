<!DOCTYPE html>
<html>
<head>
<title>LFP Dashboard</title>
<link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/dashb.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body style="font-family: Monaco, monospace;">

<ul>
    <?php
    // Include your database configuration
    require_once('config.php');

    // Retrieve user's ID from the URL query parameter if it exists, otherwise set it to 0
    $user_id = isset($_GET['aid']) ? $_GET['aid'] : 0;
    ?>
    <li>
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
    </li>
    <li><a class="active" href="dashb.php?aid=<?php echo $user_id; ?>">Home</a></li>
    <li><a href="archives.php?aid=<?php echo $user_id; ?>">Parking Logs</a></li>
    
    <?php
    // Query to retrieve user's data from the database if the user_id is not 0
    if ($user_id != 0) {
        $query = "SELECT * FROM admin WHERE aid = $user_id";
        $result = $mysqli->query($query);

        // Check if the query was successful and data was retrieved
        if ($result && $result->num_rows > 0) {
            // Fetch user's data
            $row = $result->fetch_assoc();
            $profile_picture = $row['pic'];

            // Display profile picture if available
            if (!empty($profile_picture)) {
                echo '<li style="float:right" class="profile-container">';
                echo '<img src="' . $profile_picture . '" alt="Profile Picture" class="profile-pic" onclick="toggleLogout()">';
                echo '<ul id="logoutDropdown" class="logout-dropdown">';
                echo '<li><a href="profile.php?aid=' . $user_id . '" style="padding-right: 25px;">Edit</a></li>';
                echo '<li><a href="index.php">Logout</a></li>';
                echo '</ul>';
                echo '</li>';
            }
        }
    }
    ?>
</ul>

<br>
<div class="margin">
<div class="container">
    <?php
    // Define an array to store slot information
    $slots = array(
        'Slot A' => 5,
        'Slot B' => 5,
        'Slot C' => 5,
        'Slot D' => 5,
    );

    // Loop through each slot
    foreach ($slots as $slot => $max_capacity) {
        // Query to retrieve data for the current slot from the users table
        $query = "SELECT * FROM users WHERE Slot = '$slot'";
        $result = $mysqli->query($query);
        $slotCount = $result->num_rows;
        $emptyBoxes = $max_capacity - $slotCount; // Calculate the number of empty boxes

        echo "<div>";
        echo "<h2>$slot ($slotCount/$max_capacity)</h2>";

        // Display empty boxes
        for ($i = 1; $i <= $max_capacity; $i++) {
            $slotNumber = $i; // Slot number starts from 1
            // Check if there is a row with the same slot number
            $occupied = false;
            $result->data_seek(0); // Reset result pointer to the beginning
            while ($row = $result->fetch_assoc()) {
                if ($row['Slot'] == $slot && $row['SlotNumber'] == $slotNumber) {
                    $occupied = true;
                    // Display filled box and break the loop
                    echo '<div class="vehicle-box-occupied">';
                    echo '<p>'. $row['Name'] . '</p>';
                    echo '<p>'. $row['Plate_Num'] . '</p>';
                    echo '<p>'. $row['Vehicle_Type'] . '</p>';
                    echo '<p>'. $row['Date'] . '</p>';
                    echo '<p>'. $row['Slot_Code'] . '</p>';
                    echo '<p>'. $row['TimeIn'] . '</p>';
                    echo '<p><a href="edit.php?id=' . urlencode($row['id']) . '&aid=' . $user_id . '" class="edit-button"><i class="fa fa-edit" style="font-size:30px; margin-top: 2%;"></i></a></p>';
                    echo '<p><a href="tout.php?id=' . urlencode($row['id']) . '&aid=' . $user_id . '" class="out-button"><i class="fa fa-sign-out" style="font-size:30px; margin-top: 2%;"></i></a></p>';
                    echo '</div>';
                    break;
                }
            }
            // Display "Park Here" button only if the slot is empty
            if (!$occupied) {
                echo '<div class="vehicle-box-empty"><a href="parkhere.php?slot=' . urlencode($slot) . '&slotNumber=' . $slotNumber . '&aid=' . $user_id . '" class="park-button">Park Here</a></div>';
            }
        }

        echo "</div>";
    }
    // Close the database connection after processing all slots
    $mysqli->close();
    ?>
</div>
</div>
<script>
    // Function to toggle logout dropdown
function toggleLogout() {
    var dropdown = document.getElementById("logoutDropdown");
    dropdown.classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.profile-pic')) {
        var dropdowns = document.getElementsByClassName("logout-dropdown");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>
</body>
</html>
