<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordCommand extends AbstractCommand
{
    protected static $defaultName = 'app:user:change-password';
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Changes the password of an existing user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('new_password', InputArgument::REQUIRED, 'The new password for the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $newPassword = $input->getArgument('new_password');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln('User not found!');
            return Command::FAILURE;
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));

        $this->entityManager->flush();

        $output->writeln('Password changed successfully!');

        return Command::SUCCESS;
    }
}
