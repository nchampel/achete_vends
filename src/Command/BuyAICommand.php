<?php

namespace App\Command;

use App\Service\BuyAIService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

#[AsCommand(
    name: 'app:buy-AI',
    description: 'Lance l\'achat par l\'IA des articles'
)]
class BuyAICommand extends Command
{
    public function __construct(
        private BuyAIService $buyAI,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $store = new FlockStore('/tmp');
        $lockFactory = new LockFactory($store);

        $lock = $lockFactory->createLock('app-buy-AI', 299);

        if (!$lock->acquire()) {
            $output->writeln('Le traitement est déjà en cours.');

            return Command::SUCCESS;
        }

        try {
            $output->writeln('Début du processus d\'achat...');

            $this->buyAI->buy();

            $output->writeln('Processus d\'achat terminé.');

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}