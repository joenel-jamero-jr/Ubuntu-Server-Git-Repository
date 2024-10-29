<?php
// Include your database configuration
include('config.php');

// Check if the request contains the ID of the record to delete
if (isset($_POST['id'])) {
    // Sanitize the ID to prevent SQL injection
    $id = $_POST['id'];

    // Prepare and execute the DELETE query
    $deleteQuery = "DELETE FROM archives WHERE id = ?";
    $statement = $mysqli->prepare($deleteQuery);
    $statement->bind_param('i', $id);
    $statement->execute();

    // Check if deletion was successful
    if ($statement->affected_rows > 0) {
        // Return a success response
        http_response_code(200);
        echo "Record deleted successfully.";
    } else {
        // Return an error response
        http_response_code(500);
        echo "Error deleting record.";
    }

    // Close the prepared statement and database connection
    $statement->close();
    $mysqli->close();
}
?>