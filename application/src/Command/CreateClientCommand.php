<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateClientCommand extends Command
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:client:create')
            ->setDescription('Создание нового клиента в системе')
            ->addArgument('name', InputArgument::REQUIRED,
                'Название системы (отображается на странице логина и выдачи прав)')
            ->addArgument('secret', InputArgument::REQUIRED, 'Секретный код клиента')
            ->addArgument('redirect', InputArgument::REQUIRED, 'Адрес возврата в систему клиента');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $secret = $input->getArgument('secret');
        $redirect = $input->getArgument('redirect');

        $client = new Client();
        $client->setName($name);
        $client->setSecret($secret);
        $client->setActive(true);
        $client->setRedirect($redirect);

        $this->em->persist($client);
        $this->em->flush();

        $output->writeln('<info>Клиент '.$name.' успешно создан. ID клиента: '.$client->getId().'</info>');

        return 1;
    }
}
