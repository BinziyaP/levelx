<?php
$ch = curl_init('https://api.razorpay.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
// curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // Uncomment to test

$response = curl_exec($ch);
$error = curl_error($ch);
$errno = curl_errno($ch);
$info = curl_getinfo($ch);

curl_close($ch);

if ($errno) {
    echo "Error ($errno): $error\n";
    echo "Check if IPv6 is the issue.\n";
} else {
    echo "Success! HTTP Code: " . $info['http_code'] . "\n";
}
