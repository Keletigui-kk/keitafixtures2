<?php

# Ce fichier c'est pour créer nettoyer la base de don
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteAndRecreateWithStructureAndDatabaseCommand extends Command
{
    # on fait appel à l'entity manager
    private EntityManagerInterface $entiManager;

    protected static $deFaultName = 'app:clean-db';   # c'est cette commande qu'il faut lancer pour supprimer et recreer une base de données


    public function __construct(EntityManagerInterface $entiManager)
    {
        parent::__construct();  # parent constructeur pour initialiser la fonction app:clean-db

        $this->entiManager = $entiManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Supprime et recrée la base de données avec sa structure et ses jeux de...');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       $io = new SymfonyStyle($input, $output);
       $io->section("Suppression de la base de données puis création d'une nouvelle structure et données préremplies.");

       $this->runSymfonyCommand($input, $output, 'doctrine:database:drop', true);
       $this->runSymfonyCommand($input, $output, 'doctrine:database:create');
       $this->runSymfonyCommand($input, $output, 'doctrine:migrations:migrate');
       $this->runSymfonyCommand($input, $output, 'doctrine:fixtures:load');
       # methode pour le remember me
       $this->createRememberMeTokenTable();

       $io->success('RAS => Base de données toute proprore avec ses data');

       return Command::SUCCESS;
    }

    private function runSymfonyCommand(
        InputInterface $input,
        OutputInterface $output,
        string $command,
        bool $forceOption = false
    ): void
    {
        $application = $this->getApplication();

        if(!$application){
            throw new \LogicException("No application :(");
        }
        $command = $application->find($command);

        if($forceOption){
            $input = new ArrayInput([
                '--force' => true
            ]);
        }

        $input->setInteractive(false);

        $command->run($input, $output);
    }

    # creation de la methode ou fonction createRememberTokenTable() avec la table pour créer la fonction remember me


    
    private function createRememberMeTokenTable(): void
    {
        $sqlQuery = "CREATE TABLE `rememberme_token` (
            `series`   char(88)     UNIQUE PRIMARY KEY NOT NULL,
            `value`    varchar(88)  NOT NULL,
            `lastUsed` datetime     NOT NULL,
            `class`    varchar(100) NOT NULL,
            `username` varchar(200) NOT NULL
        );";

        # on execute la query
        $this->entiManager->getConnection()->exec($sqlQuery);
    }
}
