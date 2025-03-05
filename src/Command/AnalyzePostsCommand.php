<?php

namespace App\Command;

use App\Entity\Post;
use App\Service\SentimentAnalysisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:analyze-posts',
    description: 'Analyze sentiment of existing posts',
)]
class AnalyzePostsCommand extends Command
{
    private $entityManager;
    private $sentimentService;

    public function __construct(EntityManagerInterface $entityManager, SentimentAnalysisService $sentimentService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->sentimentService = $sentimentService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        
        $io->progressStart(count($posts));
        
        foreach ($posts as $post) {
            // Analyser seulement si pas déjà analysé ou si le contenu a changé
            if ($post->getSentiment() === null) {
                $result = $this->sentimentService->analyzeFrenchText($post->getContent());
                
                $post->setSentiment($result['sentiment']);
                $post->setSentimentScore($result['score']);
                
                $this->entityManager->persist($post);
                
                // Ajouter un délai pour respecter les limites de l'API gratuite
                sleep(1);
                
                $io->progressAdvance();
            }
        }
        
        $this->entityManager->flush();
        $io->progressFinish();
        
        $io->success('All posts have been analyzed!');

        return Command::SUCCESS;
    }
}