<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailerService
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;


    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function sendEmail(string $to, string $subject, string $template, array $context = []): bool
    {
        $htmlContent = $this->twig->render($template, $context);
        $textContent = strip_tags($htmlContent);

        $email = (new Email())
            ->from('sandbox@mailtrap.io')
            ->to($to)
            ->subject($subject)
            ->text($textContent)
            ->html($htmlContent);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function sendResetPasswordEmail(string $to, string $resetToken): bool
    {
        $resetLink = $this->urlGenerator->generate('app_verif_reset_password_code', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = "Reset Your Password";

        return $this->sendEmail($to, $subject, 'emailTemplates/resetPassword.html.twig', ['token' => $resetLink]);
    }

    public function sendAccountVerificationEmail(string $to, User $user): bool
    {

        $subject = "Verify Your Account";
        $link = $this->urlGenerator->generate('app_send_code_validation', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->sendEmail($to, $subject, 'emailTemplates/welcome.html.twig', ["user" => $user, "link" => $link]);
    }
}
