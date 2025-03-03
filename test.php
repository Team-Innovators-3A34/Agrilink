<?php

$accessToken = "ya29.a0AeXRPp5nsOeovPDgUXfNUS31zK93QAHBfm97gs8oxBjxLnCJge1Ohw9QNJeCYLB4nbEZSMfihkaBWyoKuHBl8n3PMWXXHs5dBw0CREYWMIr4e1r9WMxw9kB7af1D_x-65pRmzPr4RrE4jHR4VFsCWlE4oc2jKeVITl3Ty1FOaCgYKATMSARMSFQHGX2MiOUp347JDsnUZLbpo2kYicw0175"; // Mets ton access token valide ici

$url = "https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1"; // Ajout du paramètre pour activer Meet

$data = [
    "summary" => "Réunion Google Meet",
    "description" => "Réunion générée via API",
    "start" => [
        "dateTime" => date("Y-m-d\TH:i:s", strtotime("+1 hour")),
        "timeZone" => "Europe/Paris"
    ],
    "end" => [
        "dateTime" => date("Y-m-d\TH:i:s", strtotime("+2 hour")),
        "timeZone" => "Europe/Paris"
    ],
    "conferenceData" => [
        "createRequest" => [
            "requestId" => uniqid(), 
            "conferenceSolutionKey" => ["type" => "hangoutsMeet"]
        ]
    ]
];

$options = [
    "http" => [
        "header" => "Authorization: Bearer " . $accessToken . "\r\n" .
                    "Content-Type: application/json\r\n",
        "method" => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$result = json_decode($response, true);

if (isset($result["conferenceData"]["entryPoints"][0]["uri"])) {
    echo "✅ Lien Google Meet généré : " . $result["conferenceData"]["entryPoints"][0]["uri"] . "\n";
} else {
    echo "❌ Erreur lors de la création de l'événement : " . $response;
}

?>
