<?php
namespace Rbncha\ArchieveOrder\Controller\Adminhtml\Archieve; 
 
class Order extends \Magento\Framework\App\Action\Action 
{
    protected $_context;
    protected $_orderCollectionFactory;
    protected $_indexFactory;
    protected $_indexCollection;
    public $message;
    
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Indexer\Model\IndexerFactory $indexFactory,
		\Magento\Indexer\Model\Indexer\CollectionFactory $indexCollection,
		\Magento\Framework\Message\ManagerInterface $message
    ){
        $this->_context = $context;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_indexFactory = $indexFactory;
        $this->_indexCollection = $indexCollection;
        $this->message = $message;

        parent::__construct($context);
    }
 
    /**
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$orderIds = $this->getRequest()->getPost('selected');
		
	    $conn = $resource->getConnection();
	    $sql = "update sales_order_grid set archieve_order = 1 where entity_id in (".implode(',', $orderIds) .")";

	    $conn->query($sql);

    	$collection = $this->_orderCollectionFactory->create()
        	->addAttributeToSelect('*')
        	->addFieldToFilter('entity_id', ['in' => $orderIds]);

		foreach($collection as $order){
			//$order->setArchieveOrder(1);
			//$order->save();

			$orderId = $order->getId();

			$sql = "update sales_order set archieve_order = 1 where entity_id =$orderId";
	    	$conn->query($sql);
		}

		/**
		 * 
		 * Reindex the sales_order_grid table
		 * 
		 * /
		$indexerCollection = $this->_indexCollection->create();
	  	$indexIds = $indexerCollection->getAllIds();
	 
	  	foreach ($indexIds as $indexId)
	  	{
	  		echo 'indexId: ' . $indexId . '<br>';
			//$indexidarray = $this->_indexFactory->create()->load($indexId);
			//$indexidarray->reindexRow($indexid);
	  	}

	  	$indexFactory = $this->_indexFactory->create()->load('design_config_grid');
		$indexFactory->reindexRow('design_config_grid');

	  	*/

		/**
		 * Manually update sales_order_grid.archieve_order = 1
		 */
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
	    $conn = $resource->getConnection();
	    $sql = "update sales_order_grid set archieve_order = 1 where entity_id in (".implode(',', $orderIds) .")";

	    $conn->query($sql);

	    $this->message->addSuccess(__('Order(s) \' ' . implode(',', $orderIds) . '\' were archieved'));

        $this->_redirect('sales/order');
	}


	protected function _isAllowed()
    {
    	return true;
        return $this->_authorization->isAllowed('Magento_Sales::order');
    }
}