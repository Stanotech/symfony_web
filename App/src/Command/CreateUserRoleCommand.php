<?php

namespace App\Command;

use App\Entity\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class CreateUserRoleCommand extends Command
{
    protected static $defaultName = 'app:create-user-role';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:create-user-role')
            ->setDescription('Creates a new user role.')
            ->setHelp('This command allows you to create a user role...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the role: ');
        $roleName = $helper->ask($input, $output, $question);

        $role = new UserRole();
        $role->setName($roleName);

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $output->writeln('User role created successfully!');

        return Command::SUCCESS;
    }
}
