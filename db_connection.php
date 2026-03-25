<?php
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'booknest';

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
} catch (mysqli_sql_exception $e) {
    if ($host === 'localhost') {
        die("<h2>Database Connection Failed</h2><p><strong>IMPORTANT:</strong> You are hosted on Render but have not configured your Environment Variables (DB_HOST, DB_USER, DB_PASS). Because of this, the server is incorrectly trying to connect to 'localhost', which does not exist in the Render container.</p><p>Please go to your Render Dashboard -> Environment and add the credentials for your remote MySQL database (e.g. from Aiven.io).</p>");
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}
