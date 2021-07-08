<?php
namespace Rbncha\ArchieveOrder\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
class Collection extends OriginalCollection
{
    protected $_authSession;
    public function __construct(
       EntityFactory $entityFactory,
       Logger $logger,
       FetchStrategy $fetchStrategy,
       EventManager $eventManager,
       \Magento\Backend\Model\Auth\Session $authSession
      )
    {        
       $this->_authSession = $authSession;
       parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    protected function _renderFiltersBefore()
    {
        $user = $this->_authSession->getUser();
        $joinTable = $this->getTable('sales_order_grid');
        $this->getSelect()->where('main_table.archieve_order = 0');
        parent::_renderFiltersBefore();
    }
}
