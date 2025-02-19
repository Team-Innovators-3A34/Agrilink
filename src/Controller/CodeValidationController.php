<?php
// src/Controller/SendCodeValidationController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CodeValidationController extends AbstractController
{
    private MailerService $mailerService;
    private EntityManagerInterface $em;

    public function __construct(MailerService $mailerService, EntityManagerInterface $em)
    {
        $this->mailerService = $mailerService;
        $this->em = $em;
    }

    #[Route('/sendCodeValidation', name: 'app_send_code_validation', methods: ['GET', 'POST'])]
    public function sendCodeValidation(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'No user found with this email. Please recheck the email.');
                return $this->redirectToRoute('app_send_code_validation');
            } else {
                if ($user->getAccountVerification() == 'approved' && $user->getStatus() == 'approved') {
                    $this->addFlash('success', 'Your account is active. Just Login.');
                    return $this->redirectToRoute('app_login');
                } else if ($user->getStatus() == 'pending') {
                    $this->addFlash('error', 'Your account is not approved. Wait for approval.');
                    return $this->redirectToRoute('app_send_code_validation');
                } else if ($user->getAccountVerification() == 'pending' && $user->getStatus() == 'approved') {
                    $validationCode = rand(1000, 9999);
                    $user->setVerificationCode((string) $validationCode);
                    $user->setCodeExpirationDate(new \DateTime('+10 minutes'));

                    // Save the user with the updated verification code and expiration date
                    $this->em->flush();

                    $subject = "Your Verification Code";
                    $message = "Your verification code is: <strong>{$validationCode}</strong>";

                    if ($this->mailerService->sendEmail($email, $subject, 'emailTemplates/activationCode.html.twig', ["validationCode" => $validationCode, "display_name" => $user->getNom() . " " . $user->getPrenom()])) {
                        // Store email in the session
                        $request->getSession()->set('email_for_verification', $email);

                        // Redirect to verification page
                        return $this->redirectToRoute('app_verify_code_validation');
                    }
                }
            }
        }
        return $this->render('security/sendCodeValidation.html.twig', [
            'action' => 'SendCodeValidation',
        ]);
    }

    #[Route('/verifyCodeValidation', name: 'app_verify_code_validation', methods: ['GET', 'POST'])]
    public function verifyCode(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Retrieve email from session
            $email = $request->getSession()->get('email_for_verification') ?? null;

            if (!$email) {
                $this->addFlash('error', 'Email session expired');
                return $this->redirectToRoute('app_send_code_validation');
            }

            // Concatenate OTP values into a single string
            $otp1 = $request->request->get('otp1');
            $otp2 = $request->request->get('otp2');
            $otp3 = $request->request->get('otp3');
            $otp4 = $request->request->get('otp4');

            // Concatenate all the OTP values into a single string
            $enteredOtp = $otp1 . $otp2 . $otp3 . $otp4;

            // Find the user by email
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'No user found with this email. Please recheck the email.');
                return $this->redirectToRoute('app_send_code_validation');
            }

            // Check if the code is correct and has not expired
            $code = $user->getVerificationCode();
            $expirationDate = $user->getCodeExpirationDate();

            if ($code === $enteredOtp && new \DateTime() < $expirationDate) {
                // Code is valid
                $user->setAccountVerification('approved'); // Approve the user
                $user->setVerificationCode(null); // Clear the verification code
                $user->setCodeExpirationDate(null); // Clear the expiration date
                $this->em->flush();
                $this->addFlash('success', 'Account verified successfully!');
                return $this->redirectToRoute('app_login');
            } else {
                // Code is invalid or expired
                $this->addFlash('error', 'Invalid or expired code.');
            }
        }

        return $this->render('security/confirmCode.html.twig');
    }

    #[Route('/sendResetPassword', name: 'app_send_reset_password', methods: ['GET', 'POST'])]
    public function sendResetPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'No user found with this email. Please recheck the email.');
                return $this->redirectToRoute('app_send_reset_password');
            } else if ($user->getStatus() !== 'approved' && $user->getAccountVerification() !== 'approved') {
                $this->addFlash('error', 'Your account is not active. Please verify your account.');
                return $this->redirectToRoute('app_send_code_validation');
            } else if ($user->getStatus() == 'approved' && $user->getAccountVerification() == 'approved') {
                $resetToken = bin2hex(random_bytes(32)); // Generate a secure token
                $user->setResetToken($resetToken);
                $user->setResetTokenExpiresAt(new \DateTime('+20 minutes'));
                $this->em->flush();
                if ($this->mailerService->sendResetPasswordEmail($user->getEmail(), $resetToken)) {
                    $this->addFlash('success', 'A password reset link has been sent to your email.');
                    return $this->redirectToRoute('app_login');
                }
            }
        }
        return $this->render('security/sendCodeValidation.html.twig', [
            'action' => 'ResetPassword',
        ]);
    }

    #[Route('/verif-reset-password-code/{token}', name: 'app_verif_reset_password_code')]
    public function resetPassword(string $token, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid reset token.');
            return $this->redirectToRoute('app_send_reset_password');
        }

        if (new \DateTime() > $user->getResetTokenExpiresAt()) {
            $this->addFlash('error', 'Reset token has expired.');
            return $this->redirectToRoute('app_send_reset_password');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $user->setPassword($userPasswordHasher->hashPassword($user, $password));
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            $this->em->flush();
            $this->addFlash('success', 'Password reset successfully.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/resetPassword.html.twig', ['token' => $token]);
    }
}
