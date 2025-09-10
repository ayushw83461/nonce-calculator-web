<?php
/******* free to use, free to sell, free to change, per developer/owner *****/
/***** MIT License **********************************************************/

function nonce_calculator($difficulty, $customString = '', $useCustomString = false, $customNonce = '') {
    $t1 = time();
    
    if ($useCustomString && !empty($customString)) {
        $string = $customString;
        echo "<div style='margin: 10px 0; padding: 10px; background-color: #fff3e0; border-radius: 5px;'>";
        echo "<strong>Step 1:</strong> Using Custom String<br>";
        echo "<code style='word-break: break-all;'>{$string}</code><br><br>";
        $hash1 = hash("sha1", $string);
        echo "<strong>SHA-1 Hash of your string:</strong><br>";
        echo "<code>{$hash1}</code><br><br>";
        
        // Count leading zeros in the custom string's hash
        $zero_count = 0;
        while($zero_count < strlen($hash1) && $hash1[$zero_count] === '0') {
            $zero_count++;
        }
        echo "<strong>Number of leading zeros in original hash:</strong> {$zero_count}";
        echo "</div>";
    } else {
        $a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $string = d256($a);
        echo "<div style='margin: 10px 0; padding: 10px; background-color: #fff3e0; border-radius: 5px;'>";
        echo "<strong>Step 1:</strong> Generated Random String (256 characters)<br>";
        echo "<code style='word-break: break-all;'>{$string}</code>";
        echo "</div>";
        $hash1 = hash("sha1", $string);
    }

    echo "<div style='margin: 10px 0; padding: 10px; background-color: #e0f2f1; border-radius: 5px;'>";
    echo "<strong>Step 2:</strong> Initial SHA-1 Hash<br>";
    echo "<code>{$hash1}</code>";
    echo "</div>";
    
    $zero_string = str_repeat("0", $difficulty);
    
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f3e5f5; border-radius: 5px;'>";
    echo "<strong>Target:</strong> Find a hash starting with {$difficulty} zeros ({$zero_string}...)<br>";
    echo "</div>";
    
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<strong>Step 3:</strong> " . (!empty($customNonce) ? "Testing Custom Nonce" : "Searching for nonce") . "...<br><br>";
    
    // If custom nonce is provided, test it directly
    if (!empty($customNonce)) {
        $test_hash = hash("sha1", $hash1 . $customNonce);
        $actual_zeros = 0;
        while($actual_zeros < strlen($test_hash) && $test_hash[$actual_zeros] === '0') {
            $actual_zeros++;
        }
        
        echo "</div>";
        echo "<div style='margin: 10px 0; padding: 15px; background-color: #e0f2f1; border-radius: 5px; border: 2px solid #00796b;'>";
        echo "<h3 style='color: #00796b; margin-top: 0;'>Testing Custom Nonce: {$customNonce}</h3>";
        echo "<strong>Process:</strong><br>";
        echo "1. Original String: <code>{$string}</code><br>";
        echo "2. Original Hash: <code>{$hash1}</code><br>";
        echo "3. Combined (Hash + Nonce): <code>{$hash1}{$customNonce}</code><br>";
        echo "4. Final Hash: <code>{$test_hash}</code><br><br>";
        echo "<strong>Results:</strong><br>";
        echo "Leading Zeros Found: {$actual_zeros}<br>";
        if ($actual_zeros >= $difficulty) {
            echo "<div style='margin-top: 10px; padding: 10px; background-color: #e8f5e9; border-radius: 5px;'>";
            echo "<strong style='color: #2e7d32;'>✅ This nonce works!</strong> ";
            if ($actual_zeros > $difficulty) {
                echo "(Bonus: Found {$actual_zeros} zeros when only {$difficulty} were needed)";
            }
            echo "</div>";
        } else {
            echo "<div style='margin-top: 10px; padding: 10px; background-color: #ffebee; border-radius: 5px;'>";
            echo "<strong style='color: #c62828;'>❌ This nonce doesn't work.</strong> ";
            echo "Found {$actual_zeros} leading zeros, but need {$difficulty}.";
            echo "</div>";
        }
        echo "</div>";
        return;
    }
    
    // If no custom nonce, search for one
    for($i=0; $i<100000000; $i++) {
        $hash = hash("sha1", $hash1 . $i);
        if(substr($hash,0,$difficulty) === $zero_string) {
            $time_taken = time() - $t1;
            $actual_zeros = 0;
            while($actual_zeros < strlen($hash) && $hash[$actual_zeros] === '0') {
                $actual_zeros++;
            }
            
            echo "</div>";
            echo "<div style='margin: 10px 0; padding: 15px; background-color: #e8f5e9; border-radius: 5px; border: 2px solid #4caf50;'>";
            echo "<h3 style='color: #2e7d32; margin-top: 0;'>🎉 Success!</h3>";
            echo "Time taken: {$time_taken} seconds<br>";
            echo "Nonce value: {$i}<br>";
            echo "Final hash: <code>{$hash}</code><br>";
            if($actual_zeros > $difficulty) {
                echo "<strong style='color: #2e7d32;'>Bonus! Found {$actual_zeros} leading zeros</strong> (requested: {$difficulty})<br>";
            }
            echo "<br><strong>Details:</strong><br>";
            echo "1. Original string: <code>{$string}</code><br>";
            echo "2. Original hash: <code>{$hash1}</code><br>";
            echo "3. Combined (Hash + Nonce): <code>{$hash1}{$i}</code><br>";
            echo "4. Final hash: <code>{$hash}</code>";
            echo "</div>";
            return;
        }
        
        $denominator = ((int)("1".$zero_string)) - 1;
        if($denominator > 0 && is_int($i/$denominator)) {
            echo "Attempt {$i}: <code>{$hash}</code><br>";
            ob_flush();
            flush();
        }
    }
    
    echo "</div>";
    echo "<div style='margin: 10px 0; padding: 15px; background-color: #ffebee; border-radius: 5px; border: 2px solid #f44336;'>";
    echo "<h3 style='color: #c62828; margin-top: 0;'>Maximum attempts reached</h3>";
    echo "Could not find a matching hash within 100,000,000 attempts.<br>";
    echo "Try reducing the difficulty level or using a different input string.";
    echo "</div>";
}

function d256($a) {
    $l = strlen($a) - 1;
    $s = str_split($a);
    $x = "";
    for($i=0; $i<256; $i++) {
        $j = random_int(0,$l);
        shuffle($s);
        $x .= $s[$j];
    }
    return $x;
}
