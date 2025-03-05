<?php

namespace App\Controller\GestionUser;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\User;
use App\Service\MailerService;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $em;
    private $mailer;

    public function __construct(MailerService $mailer, private UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $password = $request->getPayload()->getString('password');

        if (!$email && !$password) {
            throw new CustomUserMessageAuthenticationException('L\'email et le mot de passe ne peuvent pas être vides.');
        }

        if (!$email) {
            throw new CustomUserMessageAuthenticationException('L\'email ne peut pas être vide.');
        }

        if (!$password) {
            throw new CustomUserMessageAuthenticationException('Le mot de passe ne peut pas être vide.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Aucun compte trouvé avec cet email.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $user->setFailedLoginAttempts($user->getFailedLoginAttempts() + 1);
            if ($user->getFailedLoginAttempts() >= 3) {
                $user->setLockUntil(new \DateTime('+15 minutes'));
            }
            $this->em->flush();
            throw new CustomUserMessageAuthenticationException('Mot de passe invalide.');
        }

        if ($user->getLockUntil() && $user->getLockUntil() > new \DateTime()) {
            throw new CustomUserMessageAuthenticationException('Votre compte est temporairement verrouillé. Essayez plus tard.');
        }

        if ($user->getStatus() === 'hide') {
            throw new CustomUserMessageAuthenticationException('Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        }

        if ($user->getAccountVerification() == 'pending') {
            throw new CustomUserMessageAuthenticationException('Votre compte n\'a pas encore été vérifié. Veuillez vérifier votre compte.');
        }


        // Reset failed login attempts if no 2FA required
        $user->setFailedLoginAttempts(null);
        $user->setLockUntil(null);
        $this->em->flush();

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }




    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();

        if ($user->is2FA()) {
            // Generate and send the 2FA code
            $validationCode = random_int(1000, 9999);
            $user->setCode2FA($validationCode);
            $user->setCode2FAexpiry(new \DateTime('+15 minutes'));
            $this->em->flush();

            // Send the 2FA code via email
            $subject = "Your Verification Code";
            $this->mailer->sendEmail(
                $user->getEmail(),
                $subject,
                'emailTemplates/activationCode.html.twig',
                [
                    "validationCode" => $validationCode,
                    "display_name" => $user->getNom() . " " . $user->getPrenom(),
                ]
            );
            $request->getSession()->set('email_for_verification', $user->getEmail());
            $request->getSession()->set('action', '2FA');

            // Redirect to the 2FA verification route
            return new RedirectResponse($this->urlGenerator->generate('app_verify_code_validation'));
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
        } else {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
