<?php

namespace App\Service;

class FallbackTipService
{
    private $tips = [
        "Envisagez la plantation complémentaire pour éloigner naturellement les parasites.",
        "Installez des systèmes d'irrigation goutte-à-goutte pour économiser l'eau.",
        "Ajoutez du compost organique pour enrichir le sol en nutriments essentiels.",
        "Taillez régulièrement vos plantes pour favoriser une croissance saine et une meilleure production.",
        "Évitez le labour excessif pour préserver la structure du sol et favoriser la biodiversité.",
        "Utilisez des engrais naturels comme le fumier ou le compost pour éviter les produits chimiques nocifs.",
        "Attirez les insectes pollinisateurs en plantant des fleurs mellifères.",
        "Plantez des haies ou installez des coupe-vents pour protéger vos cultures des vents forts.",
        "Évitez l’arrosage en plein soleil pour limiter l’évaporation et maximiser l’absorption de l’eau.",
        "Pratiquez le paillage hivernal pour protéger les racines des plantes contre le gel.",
        "Associez les cultures intelligemment pour maximiser l'espace et réduire les maladies.",
        "Surveillez régulièrement vos plantes pour détecter rapidement les signes de maladies ou de ravageurs.",
        "Utilisez des filets ou des barrières physiques pour protéger vos plantes des nuisibles.",
        "Évitez la monoculture pour préserver la biodiversité et réduire les risques de maladies.",
        "Arrosez tôt le matin ou tard le soir pour minimiser la perte d’eau par évaporation.",
        "Récoltez vos fruits et légumes au bon moment pour maximiser leur saveur et leur valeur nutritive.",
        "Favorisez la biodiversité en intégrant des plantes indigènes et des refuges pour la faune.",
        "Utilisez la rotation des cultures pour prévenir les maladies et améliorer la santé du sol.",
        "Appliquez du paillis pour conserver l'humidité et supprimer les mauvaises herbes.",
        "Testez le pH de votre sol avant de planter pour assurer des conditions de croissance optimales."
    ];

    public function getRandomTip(): string
    {
        return $this->tips[array_rand($this->tips)];
    }
}
