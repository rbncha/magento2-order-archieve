<?php

namespace Rbncha\ArchieveOrder\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
	/**
	 * $obj = \Magento\Framework\App\ObjectManager::getInstance();
     * $helper = $obj->create('\Rbncha\ArchieveOrder\Helper\Data');
     * $helper->debug(__FILE__, 'error', 'saveorder.log');
     */
	public function debug($message, $type = 'error', $filename = 'archieve_order', $logDir = BP . '/var/log/')
	{
		$type = $type == 'error' ? 'err' : $type;
		$writer = new \Zend\Log\Writer\Stream($logDir . $filename);
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->$type($message);
	}
	
}