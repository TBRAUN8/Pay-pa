<?php
// captcha_verify.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cf-turnstile-response'])) {
    $turnstileSecretKey = '0x4AAAAAAAxfmQVFy8n6Hh7nyJ66nVu4X2w'; // Replace with your actual Turnstile secret key
    $token = $_POST['cf-turnstile-response'];
    $verifyUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    
    // Prepare data for POST request
    $data = http_build_query([
        'secret' => $turnstileSecretKey,
        'response' => $token,
    ]);

    // Initialize cURL session for verification
    $curl = curl_init($verifyUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        exit('Error verifying captcha response: ' . $error);
    }

    $result = json_decode($response, true);

    if ($result['success']) {
        // Captcha verified successfully, redirect or proceed with the original script logic
        $redirectUrl = $_POST['redirectUrl'] ?? 'fallback_url_if_not_set.php'; // Fallback URL if not set
        header("Location: $redirectUrl");
        exit;
    } else {
        // Captcha verification failed, handle accordingly
        echo 'Captcha verification failed. Please go back and try again.';
        // Optional: redirect back to captcha page or log for further inspection
    }
} else {
    // Invalid request method or missing captcha response
    echo 'Invalid request. Please complete the captcha verification.';
}
?>
