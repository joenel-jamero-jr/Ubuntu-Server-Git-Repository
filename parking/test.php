<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interactive Calendar of Activities</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .calendar {
        width: 80%;
        margin: 0 auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    .today {
        background-color: #ffc;
    }
    .month-year {
        text-align: center;
        margin-bottom: 10px;
    }
    .nav-btn {
        margin: 10px;
        padding: 5px 10px;
        border: 1px solid #ccc;
        background-color: #f2f2f2;
        cursor: pointer;
    }
    .activity-modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .activity-modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
</head>
<body>

<?php
// Get current month and year
if (isset($_GET['month']) && isset($_GET['year'])) {
    $currentMonth = $_GET['month'];
    $currentYear = $_GET['year'];
} else {
    $currentMonth = date('n');
    $currentYear = date('Y');
}

// Previous and next month/year
$prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
$prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
$nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
$nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

// Generate calendar table
$firstDayOfMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$daysInMonth = date('t', $firstDayOfMonth);
$dayOfWeek = date('w', $firstDayOfMonth);

// Month name
$monthName = date('F', $firstDayOfMonth);
?>

<div class="calendar">
    <h2 class="month-year"><?php echo $monthName . ' ' . $currentYear; ?></h2>
    <div class="navigation">
        <a class="nav-btn" href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>">Previous Month</a>
        <a class="nav-btn" href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>">Next Month</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tbody id="calendar-body">
            <?php
            // Create empty cells for the first week
            echo "<tr>";
            for ($i = 0; $i < $dayOfWeek; $i++) {
                echo "<td></td>";
            }

            // Loop through each day of the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                if ($dayOfWeek == 7) {
                    echo "</tr><tr>";
                    $dayOfWeek = 0;
                }
                $date = mktime(0, 0, 0, $currentMonth, $day, $currentYear);
                $dateFormatted = date('Y-m-d', $date);
                $activityClass = ""; // Class for highlighting days with activities

                // Check if there is an activity for the current date
                // You need to modify this part to check the database for activities on $dateFormatted
                // Example: $activityFound = checkActivity($dateFormatted);
                $activityFound = false; // Assume no activity found for demonstration

                if ($activityFound) {
                    $activityClass = " class='activity'";
                }

                // Check if the current date is today
                $todayClass = "";
                if ($dateFormatted == date('Y-m-d')) {
                    $todayClass = " class='today'";
                }

                // Output the day cell
                echo "<td{$activityClass}{$todayClass}><button class='add-activity-btn' data-date='{$dateFormatted}'>$day</button></td>";

                $dayOfWeek++;
            }

            // Fill in empty cells for the last week
            if ($dayOfWeek != 7) {
                for ($i = $dayOfWeek; $i < 7; $i++) {
                    echo "<td></td>";
                }
            }

            echo "</tr>";
            ?>
        </tbody>
    </table>
</div>

<!-- Activity Modal -->
<div id="activity-modal" class="activity-modal">
    <div class="activity-modal-content">
        <span class="close">&times;</span>
        <h3>Add Activity</h3>
        <form id="activity-form">
            <input type="hidden" id="activity-date" name="date" value="">
            <label for="activity-name">Activity Name:</label>
            <input type="text" id="activity-name" name="name" required>
            <button type="submit">Add</button>
        </form>
    </div>
</div>

<script>
// Get the modal
var modal = document.getElementById("activity-modal");

// Get the button that opens the modal
var btns = document.getElementsByClassName("add-activity-btn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
for (var i = 0; i < btns.length; i++) {
    btns[i].onclick = function() {
        var date = this.getAttribute("data-date");
        document.getElementById("activity-date").value = date;
        modal.style.display = "block";
    }
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Handle form submission
document.getElementById("activity-form").onsubmit = function(event) {
    event.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Activity added successfully
                alert("Activity added successfully!");
                modal.style.display = "none";
                // You may choose to reload the page or update the calendar dynamically
            } else {
                // Error adding activity
                alert("Error adding activity. Please try again.");
            }
        }
    };
    xhr.open("POST", "add_activity.php", true);
    xhr.send(formData);
};
</script>

</body>
</html>


