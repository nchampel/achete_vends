<?php

namespace App\Command;

use App\Service\BuyAIService;
use App\Service\ResourceService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

#[AsCommand(
    name: 'app:repop',
    description: 'Fait repoper les ressources'
)]
class RepopCommand extends Command
{
    public function __construct(
        private ResourceService $resourceService,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $store = new FlockStore('/tmp');
        $lockFactory = new LockFactory($store);

        $lock = $lockFactory->createLock('app-repop', 299);

        if (!$lock->acquire()) {
            $output->writeln('Le traitement est déjà en cours.');

            return Command::SUCCESS;
        }

        try {
            $this->logger->info('Début de app:repop');
            $output->writeln('Début du repopage de ressources...');

            $this->resourceService->repop();

            $output->writeln('Processus de repopage terminé.');

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}