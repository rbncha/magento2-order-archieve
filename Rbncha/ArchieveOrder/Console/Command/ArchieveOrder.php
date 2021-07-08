<?php

namespace Rbncha\ArchieveOrder\Console\Command;

use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Registry;

class ArchieveOrder extends Command
{

    public function __construct(
        State $state
    ) {
        $this->state = $state;
        parent::__construct();
    }

    const ORDER_ID = 'orderId';

    protected function configure()
    {
        $this->setName('archieve:order')
            ->setDescription('Archieve Order');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('adminhtml');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cron = $objectManager->get('Rbncha\ArchieveOrder\Cron\Archieve');

        try {
            $cron->execute();
            $output->writeln('All done');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
