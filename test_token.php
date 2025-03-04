<?php

$accessToken = "ya29.a0AeXRPp5nsOeovPDgUXfNUS31zK93QAHBfm97gs8oxBjxLnCJge1Ohw9QNJeCYLB4nbEZSMfihkaBWyoKuHBl8n3PMWXXHs5dBw0CREYWMIr4e1r9WMxw9kB7af1D_x-65pRmzPr4RrE4jHR4VFsCWlE4oc2jKeVITl3Ty1FOaCgYKATMSARMSFQHGX2MiOUp347JDsnUZLbpo2kYicw0175"; // Mets ton token ici

$url = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=" . $accessToken;

$response = file_get_contents($url);
$data = json_decode($response, true);

if (isset($data['error'])) {
    echo "❌ Token invalide ou expiré : " . $data['error'];
} else {
    echo "✅ Token valide !";
}

?>
