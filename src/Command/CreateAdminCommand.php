<?php

namespace App\Command;

use App\Manager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;


class CreateAdminCommand extends ContainerAwareCommand
{
    /**
     * @var Manager\UserManager
     */
    private $userManager;

    /**
     * @var Manager\RoleManager
     */
    private $roleManager;

    /**
     * CreateAdminCommand constructor.
     * @param Manager\UserManager $userManager
     */
    public function __construct(Manager\UserManager $userManager, Manager\RoleManager $roleManager)
    {
        $this->userManager = $userManager;
        $this->roleManager = $roleManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:create-admin')
            ->setDescription('Creates admin user.')
            ->setHelp('Create admin user form credentials specified in environment variables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email =  $this->getContainer()->getParameter('admin_email');
        $password =  $this->getContainer()->getParameter('admin_password');
        $phone_number = 0;
        $validator = Validation::createValidator();

        // Validate email
        $violations = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (0 !== count($violations)) {
            $io->title('Email is invalid.');
            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }
        }

        // Validate password
        $violations = $validator->validate($password, [
            new Assert\NotBlank(),
        ]);

        if (0 !== count($violations)) {
            $io->title('Password is invalid.');

            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }
        }

        $user = $this->userManager->findOneBy(['email' => $email]);

        if (!$user) {
            $user = $this->userManager->create();
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setFirstName('Admin');
            $user->setLastName('Admin');
            $user->setPhone($phone_number);
            $role = $this->roleManager->find(1);
            $user->setRoles([$role]);
        }

        $this->userManager->persist($user);
    }
}
