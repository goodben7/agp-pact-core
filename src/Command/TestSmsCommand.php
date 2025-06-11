<?php 

namespace App\Command;

use App\Entity\Notification;
use App\Message\SendNotificationMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TestSmsCommand extends Command
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:test-sms')
            ->setDescription('Test sending SMS notification via message bus');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $notification = new Notification();
        $notification->setRecipient('+243828120996');
        $notification->setBody('Test message via message bus');
        $notification->setRecipientType(Notification::RECIPIENT_TYPE_PHONE);
        $notification->setSentVia(Notification::SENT_VIA_SMS);

        try {
            $message = new SendNotificationMessage($notification);
            $this->messageBus->dispatch($message);
            
            $output->writeln('Notification message dispatched successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: '.$e->getMessage().'</error>');
            return Command::FAILURE;
        }
    }
}