<?php

$clientId = "167398771817-h9q6893dkchc1cf42sr5liu9cvj4fs55.apps.googleusercontent.com"; // Remplace avec ton Client ID
$clientSecret = "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"; // Remplace avec ton Client Secret
$refreshToken = "1//04hbmxHVspcGDCgYIARAAGAQSNwF-L9IrsTWCSaNBkjyutN_1_Myi4BqfL0t81754aa90ciG66oVUDhSIKV1N2PqV8UZtpdKfvoE"; // Ton refresh token

// URL pour rafraîchir le token
$url = "https://oauth2.googleapis.com/token";

// Données de la requête
$data = [
    "client_id" => $clientId,
    "client_secret" => $clientSecret,
    "refresh_token" => $refreshToken,
    "grant_type" => "refresh_token"
];

// Effectuer la requête avec cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$tokenInfo = json_decode($response, true);

if (isset($tokenInfo['access_token'])) {
    echo "Nouvel Access Token : " . $tokenInfo['access_token'];
} else {
    echo "Erreur : " . $response;
}

?>
