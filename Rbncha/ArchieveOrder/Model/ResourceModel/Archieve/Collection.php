<?php
/**
 * @author Rubin Shrestha
 */
namespace Rbncha\ArchieveOrder\Model\ResourceModel\Archieve;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

	public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Rbncha\ArchieveOrder\Model\ResourceModel\Archieve::class
    ) {
    	
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

}

