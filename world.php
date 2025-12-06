<?php
// Add error reporting at the VERY TOP
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Output error in a way JavaScript can display
    echo '<div class="error">Connection failed: ' . htmlspecialchars($conn->connect_error) . '</div>';
    echo '<div class="error">Check: 1) MySQL running 2) User/password correct 3) Database exists</div>';
    exit();
}

// Get parameters
$lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'countries';
$country = isset($_GET['country']) ? trim($_GET['country']) : '';

// Debug: Show what we received
if ($country === '') {
    // Show all countries for empty search
    $sql = "SELECT * FROM countries LIMIT 50";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['name']) . " is ruled by " . htmlspecialchars($row['head_of_state']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='no-results'>Database is empty or query failed</div>";
    }
    $conn->close();
    exit();
}

// Escape input for security
$safe_country = $conn->real_escape_string($country);

if ($lookup === 'countries') {
    // COUNTRY LOOKUP
    $sql = "SELECT name, continent, independence_year, head_of_state 
            FROM countries 
            WHERE name LIKE '%$safe_country%' 
            ORDER BY name";
    
    $result = $conn->query($sql);
    
    if ($result === false) {
        // Query failed
        echo "<div class='error'>Query failed: " . htmlspecialchars($conn->error) . "</div>";
    } elseif ($result->num_rows > 0) {
        echo "<h2>Country Results</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr><th>Name</th><th>Continent</th><th>Independence</th><th>Head of State</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        
        while ($row = $result->fetch_assoc()) {
            $independence = !empty($row['independence_year']) ? $row['independence_year'] : '';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['continent']) . "</td>";
            echo "<td>" . htmlspecialchars($independence) . "</td>";
            echo "<td>" . htmlspecialchars($row['head_of_state']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='no-results'>No countries found for \"" . htmlspecialchars($country) . "\"</div>";
    }
    
} elseif ($lookup === 'cities') {
    // CITY LOOKUP
    $sql = "SELECT cities.name AS city_name, cities.district, cities.population
            FROM cities 
            INNER JOIN countries ON cities.country_code = countries.code
            WHERE countries.name LIKE '%$safe_country%'
            ORDER BY cities.population DESC";
    
    $result = $conn->query($sql);
    
    if ($result === false) {
        // Query failed
        echo "<div class='error'>Query failed: " . htmlspecialchars($conn->error) . "</div>";
    } elseif ($result->num_rows > 0) {
        echo "<h2>Cities in \"" . htmlspecialchars($country) . "\"</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr><th>City Name</th><th>District</th><th>Population</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        
        while ($row = $result->fetch_assoc()) {
            $formattedPopulation = number_format($row['population']);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['city_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['district']) . "</td>";
            echo "<td>" . $formattedPopulation . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='no-results'>No cities found for \"" . htmlspecialchars($country) . "\"</div>";
    }
} else {
    echo "<div class='error'>Invalid lookup type</div>";
}

$conn->close();
?>