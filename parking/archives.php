<!DOCTYPE html>
<html>
<head>
<title>LFP Parking Logs</title>
    <link rel="icon" type="image/x-icon" href="css/pics/1lgpnobg.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/archives.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: Monaco, monospace;" class="bg">

    <?php
// Include your database configuration
require_once('config.php');

// Retrieve user's ID from the URL query parameter if it exists, otherwise set it to a default value
$user_id = isset($_GET['aid']) ? $_GET['aid'] : 0;
?>

<ul>
    <li>
        <img src="css/pics/1lgpnobg.png" alt="LFP Logo" class="logo">
    </li>
    <li><a href="dashb.php?aid=<?php echo $user_id; ?>">Home</a></li>
    <li><a class="active" href="archives.php?aid=<?php echo $user_id; ?>">Parking Logs</a></li>

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
</li>

</ul>

<div class="content-container">
    <div class="search-container">
        <h2 class="h-w">Parking Logs</h2>
        <a class="clear-button" href="#" onclick="clearFilters()">Clear</a>
        <input type="text" id="search" placeholder="Search Name/PlateNumber" onkeyup="handleEnter(event)">
        <button class="search-button" onclick="search()">Search <i class="fa fa-search"></i></button>
        <select style="float:right" id="sort-by" onchange="sort()">
            <option value="none">Sort by:</option>
            <option value="Car">Car</option>
            <option value="Motorcycle">Motorcycle</option>
            <option value="Slot A">Slot A</option>
            <option value="Slot B">Slot B</option>
            <option value="Slot C">Slot C</option>
            <option value="Slot D">Slot D</option>
        </select>
        <input style="float:right" type="text" id="date-filter" placeholder="Enter Date" onkeyup="handleEnter(event)">
        <button class="generate-report-button" onclick="printData()">Generate Report</button>
    </div>
</div>

<div id="print-table-container" class="table-container">
<?php
// Include your database configuration
include('config.php');

// Define the number of records per page
$recordsPerPage = 10;

// Get the current page number from the URL parameter
$pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($pageNumber - 1) * $recordsPerPage;

// Default query without sorting or search, ordering by ID in descending order
$query = "SELECT * FROM archives";

// Initialize search, sort, and date filter parameters
$searchTerm = '';
$sortOption = 'none';
$dateFilter = '';

// Check if search term is present
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Check if sorting option is selected
if (isset($_GET['sort']) && $_GET['sort'] !== 'none') {
    $sortOption = $_GET['sort'];
}

// Check if date filter is present
if (isset($_GET['date'])) {
    $dateFilter = $_GET['date'];
}

// Construct the WHERE clause based on search term and date filter
$whereClause = '';
$conditionsAdded = false; // Flag to track whether any condition has been added

if (!empty($searchTerm)) {
    $whereClause .= "(Name LIKE '%$searchTerm%' OR Plate_Num LIKE '%$searchTerm%' OR Date LIKE '%$searchTerm%' OR Slot LIKE '%$searchTerm%')";
    $conditionsAdded = true; // Set flag to true
}

if (!empty($dateFilter)) {
    if ($conditionsAdded) {
        $whereClause .= " AND ";
    }
    $whereClause .= "Date = '$dateFilter'";
}

// Append the WHERE clause to the query if it's not empty
if (!empty($whereClause)) {
    $query .= " WHERE $whereClause";
}


// Add sorting option to the query
if ($sortOption !== 'none') {
    if ($sortOption == 'Car' || $sortOption == 'Motorcycle' || $sortOption == 'Slot A' || $sortOption == 'Slot B' || $sortOption == 'Slot C' || $sortOption == 'Slot D') {
        // Handle vehicle type and slot sorting
        $query .= " ORDER BY Vehicle_Type = '$sortOption' DESC, Slot = '$sortOption' DESC, Date DESC";
    }
}

// Complete the query and add pagination
$query .= " LIMIT $offset, $recordsPerPage";

$result = $mysqli->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        // Output table
        echo "<table>";
        echo "<tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Plate #</th>
                <th>Vehicle Type</th>
                <th>Slot Code</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Action</th>
                <th>Action</th>
            </tr>";

        $counter = 1; // Initialize the counter

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $counter . '</td>'; // Display the counter
            echo '<td>' . $row['Name'] . '</td>';
            echo '<td>' . $row['Contact_Number'] . '</td>';
            echo '<td>' . $row['Plate_Num'] . '</td>';
            echo '<td>' . $row['Vehicle_Type'] . '</td>';
            echo '<td>' . $row['Slot_Code'] . '</td>';
            echo '<td>' . $row['Date'] . '</td>';
            echo '<td>' . $row['TimeIn'] . '</td>';
            echo '<td>' . $row['TimeOut'] . '</td>';
            echo '<td> <button class="more-button" style="font-size:14px" onclick="showDetails(\'' . $row['Name'] . '\', \'' . $row['Contact_Number'] . '\', \'' . $row['Plate_Num'] . '\', \'' . $row['Vehicle_Type'] . '\', \'' . $row['Slot'] . '\', \'' . $row['SlotNumber'] . '\', \'' . $row['Slot_Code'] . '\', \'' . $row['Date'] . '\', \'' . $row['TimeIn'] . '\', \'' . $row['TimeOut'] . '\', \'' . $row['In_By'] . '\', \'' . $row['Out_By'] . '\')">More Details</button> </td>';
            echo '<td>
                    <a href="edit_arch.php?id=' . urlencode($row['id']) . '&aid=' . $user_id . '" class="edit-button"><i class="fa fa-edit" style="font-size:20px"></i></a>
                    <button class="out-button" style="font-size:20px" onclick="confirmDelete(\'' . $row['id'] . '\')"><i class="fa fa-trash"></i></button>
                    </td>';
            echo '</tr>';

            $counter++; // Increment the counter
        }

        echo "</table>";
    } else {
        echo "<p>No data found in the archives.</p>";
    }
} else {
    echo "Error retrieving data from the database: " . $mysqli->error;
}


// Close the database connection
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

    // Function to clear filters and reload the page
    function clearFilters() {
        // Set search input and sort dropdown to their default values
        document.getElementById('search').value = '';
        document.getElementById('sort-by').value = 'none';

        // Retrieve the user's ID from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('aid');

        // Reload the page with the user's ID included in the URL
        window.location.href = 'archives.php?aid=' + userId;
    }

 // Function to handle search
function search() {
    const searchTerm = document.getElementById('search').value;
    const sortBy = document.getElementById('sort-by').value;
    const dateFilter = document.getElementById('date-filter').value;

    // Retrieve the user's ID from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('aid');

    // Construct the URL with search, sorting, and date filtering parameters
    let url = 'archives.php?aid=' + userId + '&search=' + encodeURIComponent(searchTerm);

    // Add sorting parameter if selected
    if (sortBy !== 'none') {
        url += '&sort=' + sortBy;
    }

    // Add date filter parameter if provided
    if (dateFilter.trim() !== '') {
        url += '&date=' + encodeURIComponent(dateFilter);
    }

    // Reload the page with the constructed URL
    window.location.href = url;
}

// Set initial values for search and sort
document.getElementById('search').value = '<?php echo $searchTerm; ?>';
document.getElementById('sort-by').value = '<?php echo $sortOption; ?>';
document.getElementById('date-filter').value = '<?php echo $dateFilter; ?>';

// Function to handle date filtering
function filterByDate() {
    // Retrieve the entered date
    const enteredDate = document.getElementById('date-filter').value.trim();

    // Trigger the search function with the entered date as part of the search query
    search();
}

// Function to print data
function printData() {
    var printContents = document.getElementById('print-table-container').innerHTML;
    var dateFilter = document.getElementById('date-filter').value.trim();
    var header = "<h2>Parking Logs";
    if (dateFilter !== '') {
        header += " of " + dateFilter;
    }
    header += "</h2>";

    // Create a container for the printable content
    var printableContainer = document.createElement('div');
    printableContainer.innerHTML = header + printContents;

    // Remove the last two headers
    var tableHeaders = printableContainer.querySelectorAll('th');
    if (tableHeaders.length > 1) {
        var lastHeaderIndex = tableHeaders.length - 1;
        tableHeaders[lastHeaderIndex].parentNode.removeChild(tableHeaders[lastHeaderIndex]);
        tableHeaders[lastHeaderIndex - 1].parentNode.removeChild(tableHeaders[lastHeaderIndex - 1]);
    }

    // Remove the last two columns in each row
    var tableRows = printableContainer.querySelectorAll('tr');
    tableRows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            var lastCellIndex = cells.length - 1;
            row.removeChild(cells[lastCellIndex]);
            row.removeChild(cells[lastCellIndex - 1]);
        }
    });

    // Create a new window for printing
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>' + document.title + '</title>');
    printWindow.document.write('<style>@media print { table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid black; font-family: Arial, sans-serif; padding: 10px; } th { font-weight: bold; } }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<table>');
    printWindow.document.write(header);
    printWindow.document.write(printableContainer.querySelector('table').innerHTML);
    printWindow.document.write('</table>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Function to handle Enter key in the search input
function handleEnter(event) {
    if (event.key === 'Enter') {
        search();
    }
    
}
    // Call search function when sorting dropdown changes
    document.getElementById('sort-by').addEventListener('change', function() {
        search();
    });
    
function showDetails(name, contact, plateNum, vehicleType, slot, slotNumber, slotCode, date, timeIn, timeOut, inBy, outBy) {
    // Construct the HTML content for the modal
    const htmlContent = `
        <p><strong>Name:</strong> ${name}</p>
        <p><strong>Contact:</strong> ${contact}</p>
        <p><strong>Plate Number:</strong> ${plateNum}</p>
        <p><strong>Vehicle Type:</strong> ${vehicleType}</p>
        <p><strong>Slot:</strong> ${slot}</p>
        <p><strong>Slot Number:</strong> ${slotNumber}</p>
        <p><strong>Slot Code:</strong> ${slotCode}</p>
        <p><strong>Date:</strong> ${date}</p>
        <p><strong>Time In:</strong> ${timeIn}</p>
        <p><strong>Time Out:</strong> ${timeOut}</p>
        <p><strong>Timed In By:</strong> ${inBy}</p>
        <p><strong>Timed Out By:</strong> ${outBy}</p>
    `;

    // Display the SweetAlert2 modal
    Swal.fire({
        title: 'Complete Details',
        html: htmlContent,
        confirmButtonText: 'Close'
    });
}

    function closePopup() {
        // Find and remove the popup element from the document body
        var popup = document.querySelector('.popup');
        if (popup) {
            popup.parentNode.removeChild(popup);
        }
    }

    function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to delete this data.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteData(id);
        }
    });
}

function deleteData(id) {
    // Send AJAX request to delete.php with the ID parameter
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'delete.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Handle success response
            Swal.fire(
                'Deleted!',
                'The data has been deleted.',
                'success'
            ).then(() => {
                // Reload the page or perform any other action
                location.reload();
            });
        } else {
            // Handle error response
            Swal.fire(
                'Error!',
                'Failed to delete the data.',
                'error'
            );
        }
    };
    xhr.send('id=' + id);
}

</script>
</body>
</html>