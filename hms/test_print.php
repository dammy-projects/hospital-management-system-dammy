<?php
// Simple test to check if API returns insurance data when logged in
session_start();

// You need to be logged in to your HMS system for this test to work
if (!isset($_SESSION['user_id'])) {
    echo "<h2 style='color: red;'>Please log in to HMS first, then visit this page</h2>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
    exit;
}

echo "<h2>Testing Patient Insurance API (Authenticated)</h2>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

// Test the API endpoint that JavaScript calls
$testUrl = 'http://localhost/hms/controllers/PatientController.php?action=get&id=1';

// Create a context to include session cookies
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Cookie: ' . $_SERVER['HTTP_COOKIE']
    ]
]);

echo "<h3>Testing API Call:</h3>";
echo "<p>URL: $testUrl</p>";

$response = file_get_contents($testUrl, false, $context);

if ($response === false) {
    echo "<p style='color: red;'>❌ Failed to call API</p>";
} else {
    echo "<h4>✅ API Response received:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<h4>Parsed Response:</h4>";
        
        if ($data['success']) {
            echo "<p style='color: green;'>✅ API call successful</p>";
            
            if (isset($data['insurances'])) {
                echo "<p><strong>Number of insurances:</strong> " . count($data['insurances']) . "</p>";
                
                if (count($data['insurances']) > 0) {
                    echo "<h5>✅ Insurance Data Found:</h5>";
                    foreach ($data['insurances'] as $i => $insurance) {
                        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                        echo "<h6>Insurance " . ($i + 1) . ":</h6>";
                        echo "<ul>";
                        foreach ($insurance as $key => $value) {
                            echo "<li><strong>$key:</strong> " . htmlspecialchars($value ?? 'NULL') . "</li>";
                        }
                        echo "</ul>";
                        echo "</div>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠️ Insurance array is empty</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ No 'insurances' key in response</p>";
            }
            
            if (isset($data['patient'])) {
                echo "<h5>Patient data exists: ✅</h5>";
                echo "<p>Patient: " . htmlspecialchars($data['patient']['first_name'] ?? 'Unknown') . " " . htmlspecialchars($data['patient']['last_name'] ?? '') . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ API returned error: " . htmlspecialchars($data['message']) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON response</p>";
    }
}
?>

<hr>
<h3>Instructions:</h3>
<ol>
    <li><strong>Make sure you're logged in</strong> to HMS in another tab</li>
    <li><strong>Refresh this page</strong> if you see "Please log in" message</li>
    <li><strong>Check the results above</strong> - it should show insurance data for Patient ID 1</li>
    <li><strong>If it works here but not in print</strong>, the issue is in the JavaScript</li>
    <li><strong>If it doesn't work here</strong>, the API still has issues</li>
</ol> 