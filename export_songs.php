<?php

// Database credentials
$db_host = '192.168.56.56';
$db_name = 'karani';
$db_user = 'homestead';
$db_pass = 'secret';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select data from the songs table
$sql = "SELECT title, youtube, song_writer, style, `key`, lyrics, music_notes FROM songs";
$result = $conn->query($sql);

// Open a file for writing
$fp = fopen('songs.csv', 'w');

// Add the headers to the CSV file
fputcsv($fp, array('title', 'youtube', 'description', 'song_writer', 'style', 'key', 'lyrics', 'music_notes'));

// Loop through the result set and write each row to the CSV file
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($fp, $row);
    }
}

// Close the file and the database connection
fclose($fp);
$conn->close();

echo "CSV file created successfully.";
