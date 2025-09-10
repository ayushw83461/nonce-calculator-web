<?php
/******* free to use, free to sell, free to change, per developer/owner *****/
/***** MIT License **********************************************************/

set_time_limit(300);//required for long calculation times
include "functions.php";//include the functions

// Get parameters from form
$stringType = isset($_POST['stringType']) ? $_POST['stringType'] : 'random';
$inputString = isset($_POST['inputString']) ? $_POST['inputString'] : '';
$searchType = isset($_POST['searchType']) ? $_POST['searchType'] : 'zeros';
$targetValue = isset($_POST['targetValue']) ? $_POST['targetValue'] : '4';

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
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
            line-height: 1.6;
        }
        .explanation {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .form-container {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .option-container {
            margin-left: 20px;
            padding: 10px;
        }
        .result-section {
            background-color: #e6ffe6;
            padding: 20px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .progress-section {
            background-color: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            padding: 15px;
            border-radius: 5px;
        }
        .failure {
            color: #721c24;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 5px;
        }
        .hash {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
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

        function toggleSearchType(type) {
            document.getElementById('zerosOption').style.display = type === 'zeros' ? 'block' : 'none';
            document.getElementById('customOption').style.display = type === 'custom' ? 'block' : 'none';
            
            // Reset values when switching
            if (type === 'zeros') {
                document.getElementById('customPattern').value = '';
            } else {
                document.getElementById('zeroCount').value = '4';
            }
        }
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
                <h3>2. Choose Hash Search Method</h3>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="radio" name="searchType" value="zeros" <?php echo ($searchType === 'zeros' ? 'checked' : ''); ?> onclick="toggleSearchType('zeros')">
                        Find hash with leading zeros
                    </label>
                    <label style="display: block;">
                        <input type="radio" name="searchType" value="custom" <?php echo ($searchType === 'custom' ? 'checked' : ''); ?> onclick="toggleSearchType('custom')">
                        Find hash starting with specific pattern
                    </label>
                </div>
                
                <div id="zerosOption" class="option-container" style="<?php echo ($searchType === 'zeros' ? '' : 'display: none;'); ?>">
                    <label for="zeroCount">Number of leading zeros (1-8):</label><br>
                    <input type="number" id="zeroCount" name="targetValue" min="1" max="8" value="<?php echo $searchType === 'zeros' ? htmlspecialchars($targetValue) : '4'; ?>">
                    <p class="note">Higher numbers will take longer to calculate!</p>
                </div>
                
                <div id="customOption" class="option-container" style="<?php echo ($searchType === 'custom' ? '' : 'display: none;'); ?>">
                    <label for="customPattern">Starting pattern to find (e.g., 1101, abc):</label><br>
                    <input type="text" id="customPattern" name="targetValue" placeholder="Enter pattern" value="<?php echo $searchType === 'custom' ? htmlspecialchars($targetValue) : ''; ?>">
                </div>
            </div>

            <button type="submit">Calculate Nonce</button>
        </form>
    </div>

    <div class="result">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($stringType === 'random' || !empty($inputString)) {
                nonce_calculator($searchType, $targetValue, $inputString);
            } else {
                echo "<p class='failure'>Please enter a custom string or choose 'Generate random string'</p>";
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
