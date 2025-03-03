<?php
// src/Security/RoleVoter.php
namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoleVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // On peut vérifier ici le type d'objet ou l'attribut
        return in_array($attribute, ['ROLE_AGRICULTURE', 'ROLE_RECYCLING_INVESTOR', 'ROLE_RESOURCE_INVESTOR']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Si l'utilisateur a un rôle d'administrateur, on empêche l'accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return false; // L'administrateur n'a pas accès
        }

        // Sinon, on autorise l'accès si l'utilisateur possède le rôle requis
        return $this->security->isGranted($attribute);
    }
}
