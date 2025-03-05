<?php

namespace App\Service;

class FallbackTipService
{
    private $tips = [
        "Utilisez la rotation des cultures pour prévenir les maladies et améliorer la santé du sol.",
        "Appliquez du paillis pour conserver l'humidité et supprimer les mauvaises herbes.",
        "Testez le pH de votre sol avant de planter pour assurer des conditions de croissance optimales.",
        "Envisagez la plantation complémentaire pour éloigner naturellement les parasites.",
        "Installez des systèmes d'irrigation goutte-à-goutte pour économiser l'eau.",
        // Add more tips...
    ];
    
    public function getRandomTip(): string
    {
        return $this->tips[array_rand($this->tips)];
    }
}