<?php

namespace Rbncha\ArchieveOrder\Cron;

use Rbncha\ArchieveOrder\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\Storage\WriterInterface as Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Archieve
{

    protected $helper;
    protected $orders;
    protected $logFile = 'archieved-orders.log';
    protected $logType = 'info';
    protected $config;
    protected $scope;

    public function __construct(
        Helper $helper,
        Order $orders,
        Config $config,
        ScopeConfigInterface $scope
    ) {
        $this->helper = $helper;
        $this->orders = $orders;
        $this->config = $config;
        $this->scope = $scope;
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $archieve = $objectManager->get('Rbncha\ArchieveOrder\Model\Archieve');
        $archieve->cronArchieve();
    }
}
