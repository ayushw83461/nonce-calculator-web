<?php
/******* free to use, free to sell, free to change, per developer/owner *****/
/***** MIT License **********************************************************/

// Function to calculate nonce with intermediate results
function nonce_calculator($input_value, $pattern) {
    $nonce = 0;
    $startTime = microtime(true);
    $results = array();
    $checkInterval = 1000; // Check every 1000 attempts
    
    while (true) {
        $hash = sha1($input_value . $nonce);
        
        // Store intermediate result every checkInterval attempts
        if ($nonce % $checkInterval === 0) {
            $currentTime = microtime(true) - $startTime;
            $results[] = array(
                'attempts' => $nonce,
                'hash' => $hash,
                'time' => $currentTime
            );
        }
        
        // If we found the match
        if (strpos($hash, $pattern) === 0) {
            $endTime = microtime(true);
            $timeElapsed = $endTime - $startTime;
            // Add the final result
            $results[] = array(
                'attempts' => $nonce,
                'hash' => $hash,
                'time' => $timeElapsed
            );
            return array($nonce, $hash, $timeElapsed, $results);
        }
        $nonce++;
    }
}

// Function to generate a random 256-bit (32-byte) string
function d256() {
    $bytes = random_bytes(32);
    return bin2hex($bytes);
}

// Function to calculate initial hash without nonce
function calculate_initial_hash($input_value) {
    return sha1($input_value);
}
?>
