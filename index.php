<?php
/******* free to use, free to sell, free to change, per developer/owner *****/
/***** MIT License **********************************************************/

set_time_limit(300);//required for long calculation times
include "functions.php";//include the functions

// Get parameters from form
$stringType = isset($_POST['stringType']) ? $_POST['stringType'] : 'random';
$inputString = isset($_POST['inputString']) ? $_POST['inputString'] : '';
$searchType = isset($_POST['searchType']) ? $_POST['searchType'] : 'custom';
$targetValue = isset($_POST['targetValue']) ? $_POST['targetValue'] : '';

// If form is submitted, process the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate input string if random is selected
    if ($stringType === 'random') {
        $inputString = d256();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Nonce Calculator</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 0 20px;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #2c3e50;
        }
        .explanation {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .option-container {
            margin-left: 20px;
            padding: 15px;
        }
        .result-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .progress-section {
            text-align: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #4CAF50;
            color: white;
        }
        .progress-section h3 {
            margin: 0;
            font-size: 1.5em;
        }
        .search-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
        }
        .search-stats p {
            margin: 0;
            font-size: 1.1em;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .search-stats strong {
            font-size: 1.3em;
            margin-top: 5px;
        }
        .intermediate-results {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .intermediate-results h3 {
            margin: 0 0 20px 0;
            color: #2d3748;
            text-align: center;
        }
        .result-table {
            overflow-x: auto;
        }
        .result-table table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        .result-table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .result-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .result-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .result-table .hash-cell {
            font-family: monospace;
            font-size: 0.9em;
        }
        .result-table tbody tr:hover {
            background-color: #f0f9ff;
        }
        .result-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .result-card {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .result-card h4 {
            margin-top: 0;
            color: #2d3748;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .data-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .data-row label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #4a5568;
        }
        .data-row code {
            background-color: #edf2f7;
            padding: 8px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.9em;
            word-break: break-all;
        }
        .highlight code {
            background-color: #4CAF50;
            color: white;
        }
        .alert-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #45a049;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 200px;
        }
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-height: 100px;
        }
        .note {
            color: #666;
            font-size: 0.9em;
            font-style: italic;
        }
    </style>
    <script>
        function toggleInputSection(type) {
            document.getElementById('randomString').style.display = type === 'random' ? 'block' : 'none';
            document.getElementById('customString').style.display = type === 'custom' ? 'block' : 'none';
        }

        // Search type toggle removed - using only pattern search now
    </script>
</head>
<body>
    <h1>Interactive Nonce Calculator</h1>
    
    <div class="explanation">
        <h2>What is a Nonce?</h2>
        <p>A nonce (number used once) is a random number that, when combined with other data and hashed, produces a hash 
           with specific characteristics. In blockchain and cryptocurrency mining, miners need to find a nonce that, when combined 
           with the block data, produces a hash starting with a certain number of zeros (this is called the difficulty level).</p>
    </div>

    <div class="form-container">
        <form method="post">
            <div class="section">
                <h3>1. Choose Input String</h3>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="radio" name="stringType" value="random" <?php echo ($stringType === 'random' ? 'checked' : ''); ?> onclick="toggleInputSection('random')">
                        Generate random string (256 characters)
                    </label>
                    <label style="display: block;">
                        <input type="radio" name="stringType" value="custom" <?php echo ($stringType === 'custom' ? 'checked' : ''); ?> onclick="toggleInputSection('custom')">
                        Use custom string
                    </label>
                </div>
                
                <div id="randomString" class="option-container" style="<?php echo ($stringType === 'random' ? '' : 'display: none;'); ?>">
                    <p>A random 256-character string will be generated</p>
                </div>
                
                <div id="customString" class="option-container" style="<?php echo ($stringType === 'custom' ? '' : 'display: none;'); ?>">
                    <textarea name="inputString" placeholder="Enter your text here (e.g., Hello World)"><?php echo htmlspecialchars($inputString); ?></textarea>
                </div>
            </div>

            <div class="section">
                <h3>2. Enter Pattern to Search</h3>
                <div class="option-container">
                    <label for="customPattern">Starting pattern to find:</label><br>
                    <input type="text" id="customPattern" name="targetValue" placeholder="e.g., 1101, abc, def789" value="<?php echo htmlspecialchars($targetValue); ?>">
                </div>
                <input type="hidden" name="searchType" value="custom">
            </div>

            <button type="submit">Calculate Nonce</button>
        </form>
    </div>

    <div class="result-section">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($stringType === 'random' || !empty($inputString)) {
                $initialHash = calculate_initial_hash($inputString);
                list($nonce, $finalHash, $timeElapsed, $progress) = nonce_calculator($inputString, $targetValue);
                
                // Final Result section
                echo "<div class='progress-section'>";
                echo "<h3>🎯 Found a Match!</h3>";
                echo "<div class='search-stats'>";
                echo "<p>Final Attempts: <strong>" . number_format($nonce) . "</strong></p>";
                echo "<p>Total Time: <strong>" . number_format($timeElapsed, 3) . "</strong> seconds</p>";
                echo "<p>Hash Rate: <strong>" . number_format($nonce/$timeElapsed, 0) . "</strong> hashes/second</p>";
                echo "</div>";
                echo "</div>";

                // Show intermediate results
                if (!empty($progress)) {
                    echo "<div class='intermediate-results'>";
                    echo "<h3>Search Progress</h3>";
                    echo "<div class='result-table'>";
                    echo "<table>";
                    echo "<thead><tr><th>Attempt #</th><th>Time (seconds)</th><th>Sample Hash</th></tr></thead>";
                    echo "<tbody>";
                    
                    // Show only a few intermediate results for clarity
                    $totalResults = count($progress);
                    $showResults = array(
                        0, // First result
                        floor($totalResults * 0.25), // 25%
                        floor($totalResults * 0.5),  // 50%
                        floor($totalResults * 0.75), // 75%
                        $totalResults - 1 // Last result
                    );
                    
                    foreach ($showResults as $index) {
                        if (isset($progress[$index])) {
                            $result = $progress[$index];
                            echo "<tr>";
                            echo "<td>" . number_format($result['attempts']) . "</td>";
                            echo "<td>" . number_format($result['time'], 3) . "</td>";
                            echo "<td class='hash-cell'>" . substr($result['hash'], 0, 15) . "...</td>";
                            echo "</tr>";
                        }
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                    echo "</div>";
                }

                // Display all results in organized sections
                echo "<div class='result-details'>";
                
                // Input Section
                echo "<div class='result-card'>";
                echo "<h4>📝 Input Data</h4>";
                echo "<div class='data-row'><label>Input String:</label><code>" . htmlspecialchars($inputString) . "</code></div>";
                echo "<div class='data-row'><label>Target Pattern:</label><code>" . htmlspecialchars($targetValue) . "</code></div>";
                echo "</div>";

                // Hash Results Section
                echo "<div class='result-card'>";
                echo "<h4>🔍 Hash Results</h4>";
                echo "<div class='data-row'><label>Initial Hash:</label><code>" . $initialHash . "</code></div>";
                echo "<div class='data-row'><label>Final Hash:</label><code>" . $finalHash . "</code></div>";
                echo "<div class='data-row highlight'><label>Nonce Value:</label><code>" . $nonce . "</code></div>";
                echo "</div>";

                echo "</div>"; // Close result-details
            } else {
                echo "<div class='alert-message'>";
                echo "<p>⚠️ Please enter a custom string or choose 'Generate random string'</p>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <div class="explanation">
        <h2>How It Works</h2>
        <ol>
            <li>The calculator starts with either your custom string or generates a random one</li>
            <li>It creates an initial SHA-1 hash of this string</li>
            <li>It then repeatedly adds numbers (nonce values) to the hash and re-hashes until it finds a hash that matches your criteria</li>
            <li>The process continues until a matching hash is found or the maximum attempts are reached</li>
        </ol>
    </div>
</body>
</html>
