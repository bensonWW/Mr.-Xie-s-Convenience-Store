<?php

// Configuration
$baseUrl = 'https://mr-xie-s-convenience-store-main-d3awzd.laravel.cloud/api';
$credentials = [
    'email' => 'admin@mrxie.com',
    'password' => 'password'
];

echo "========================================\n";
echo "Cloud API Health Check Tool\n";
echo "Target: $baseUrl\n";
echo "========================================\n\n";

// Helper function to make requests
function makeRequest($url, $method = 'GET', $data = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return ['code' => $httpCode, 'body' => $response, 'error' => $error];
}

// 1. Check Connectivity (Public Endpoint)
echo "[1] Testing Connectivity (GET /settings)...\n";
$settingsUrl = "$baseUrl/settings";
$res = makeRequest($settingsUrl);

if ($res['code'] === 200) {
    echo "✅ Success (200 OK)\n";
    // echo "Preview: " . substr($res['body'], 0, 100) . "...\n";
} else {
    echo "❌ Failed\n";
    echo "Status: " . $res['code'] . "\n";
    echo "Error: " . $res['error'] . "\n";
    echo "Body: " . $res['body'] . "\n";
    exit(1);
}

echo "\n";

// 2. Check Authentication (Login)
echo "[2] Testing Admin Login (POST /login)...\n";
echo "User: " . $credentials['email'] . "\n";

$loginUrl = "$baseUrl/login";
$res = makeRequest($loginUrl, 'POST', $credentials);

if ($res['code'] === 200) {
    $data = json_decode($res['body'], true);
    if (isset($data['token']) || isset($data['access_token'])) {
        echo "✅ Login Successful!\n";
        $token = $data['token'] ?? $data['access_token']; // Capture for next step
        echo "Token received: " . substr($token, 0, 15) . "...\n";
    } else {
        echo "⚠️  Request OK (200) but no token found in response.\n";
        print_r($data);
    }
} elseif ($res['code'] === 401 || $res['code'] === 422) {
    echo "❌ Login Failed (Authentication Error)\n";
    echo "Status: " . $res['code'] . "\n";
    echo "Response: " . $res['body'] . "\n";
} else {
    echo "❌ Login Failed (Server/Network Error)\n";
    echo "Status: " . $res['code'] . "\n";
    echo "Error: " . $res['error'] . "\n";
    echo "Body: " . $res['body'] . "\n";
}

echo "\n";

// 3. Get User Profile (Check Role)
if (isset($token)) {
    echo "[3] Fetching User Profile (GET /user)...\n";
    $profileUrl = "$baseUrl/user";

    $ch = curl_init($profileUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $user = json_decode($response, true);
        echo "✅ Profile Fetched\n";
        echo "ID: " . ($user['id'] ?? 'N/A') . "\n";
        echo "Name: " . ($user['name'] ?? 'N/A') . "\n";
        echo "Email: " . ($user['email'] ?? 'N/A') . "\n";
        echo "Role: " . ($user['role'] ?? '⚠️ MISSING') . "\n"; // Critical check
    } else {
        echo "❌ Profile Fetch Failed ($httpCode)\n";
        echo "Body: $response\n";
    }
}

echo "\n========================================\n";
