<?php

namespace App\Command;

use App\Service\StockGeneratorService;
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
        private StockGeneratorService $stockGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $store = new FlockStore('/tmp');
        $lockFactory = new LockFactory($store);

        $lock = $lockFactory->createLock('app-generate-stock', 299);

        if (!$lock->acquire()) {
            $output->writeln('Le traitement est déjà en cours.');

            return Command::SUCCESS;
        }

        try {
            $output->writeln('Début de la génération du stock...');

            $this->stockGenerator->generate();

            $output->writeln('Génération terminée.');

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}