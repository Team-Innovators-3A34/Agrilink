<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PdfGenerator
{
    private $pdf;
    private $twig;
    private $projectDir;
    private $router;

    public function __construct(
        Pdf $pdf, 
        Environment $twig, 
        string $projectDir, 
        UrlGeneratorInterface $router
    ) {
        $this->pdf = $pdf;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
        $this->router = $router;
    }

    public function generateProfilePdf($user, $currentUser = null)
    {
        // Get posts and resources directly from user object
        $posts = $user->getPosts();
        $resources = $user->getRessources();
        
        // Generate QR code URL for the profile
        $profileUrl = $this->router->generate(
            'app_profilee', 
            ['id' => $user->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        // Render the Twig template for PDF
        $html = $this->twig->render('pdf/profile_pdf.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'resources' => $resources,
            'current_user' => $currentUser,
            'generated_at' => new \DateTime(),
            'profile_url' => $profileUrl
        ]);

        // Set some options for better PDF rendering
        $this->pdf->setOption('enable-local-file-access', true);
        $this->pdf->setOption('encoding', 'UTF-8');
        $this->pdf->setOption('page-size', 'A4');
        $this->pdf->setOption('margin-top', '10mm');
        $this->pdf->setOption('margin-right', '10mm');
        $this->pdf->setOption('margin-bottom', '10mm');
        $this->pdf->setOption('margin-left', '10mm');
        
        // Generate unique filename
        $filename = sprintf(
            'profile_%s_%s_%s.pdf',
            $user->getNom(),
            $user->getPrenom(),
            (new \DateTime())->format('Y-m-d_H-i')
        );
        
        // Generate PDF from HTML
        return new Response(
            $this->pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }
}