<?php

/**
 * @author Rubin Shrestha
 */


namespace Rbncha\ArchieveOrder\Model;

use Rbncha\ArchieveOrder\Helper\Data as Helper;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\Storage\WriterInterface as Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\ObjectManagerInterface;


class Archieve extends AbstractModel
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_helper;
    protected $_orders;
    protected $_logFile = 'archieved-orders.log';
    protected $_logType = 'info';
    protected $_config;
    protected $_scope;
    protected $_objectManager;
    protected $_lastRanDateKey = 'rbncha_archieveorder/general/archiveorder_lastRanDate';

    protected $_configInheritance = true;

    /**
     * 
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param Helper $helper
     * @param Order $orders
     * @param Config $config
     * @param ScopeConfigInterface $scope
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        Helper $helper,
        Order $orders,
        Config $config,
        ScopeConfigInterface $scope,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_orders = $orders;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_scope = $scope;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Rbncha\ArchieveOrder\Model\ResourceModel\Archieve');
    }

    /**
     * @return object
     */
    public function getCollection()
    {
        //$collection = $this->getResourceCollection()->addCategoryFilter();

        $collection = parent::getCollection();

        return $collection;
    }

    /**
     * Let's update given orders and set archieve_order = 1
     * 
     * @param orderIds array order ids
     * @return bool
     */
    public function archieveOrders(array $orderIds)
    {
        try {
            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $conn = $resource->getConnection();
            $conn->beginTransaction();
            $sql1 = "update sales_order_grid set archieve_order = 1 where entity_id in (" . implode(',', $orderIds) . ")";
            $sql2 = "update sales_order set archieve_order = 1 where entity_id in (" . implode(',', $orderIds) . ")";
            $conn->query($sql1);
            $conn->query($sql2);
            $conn->commit();
        } catch (\Exception $e) {
            //echo $e->getMessage();
            $conn->rollback();
            return false;
        }

        return true;
    }

    public function cronArchieve()
    {

        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $incrementIds = [];
        $orderIds = [];
        //$lastRanDate = $this->_scope->getValue($this->_lastRanDateKey, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $enabled = $this->_scope->getValue('rbncha_archieveorder/general/archiveorder', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $days = $this->_scope->getValue('rbncha_archieveorder/general/archiveorder_days', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $statuses = $this->_scope->getValue('rbncha_archieveorder/general/archiveorder_statuses', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $today = date_create(date('Y-m-d'));

        date_add($today, date_interval_create_from_date_string("-$days days"));
        $today = date_format($today, 'Y-m-d') . ' 23:59:59';

        if (!$enabled || empty($days)) return false;

        $statuses = "'" . str_replace(',', "','", $statuses) . "'";

        if ($statuses == "''") $statuses = false;

        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $conn = $resource->getConnection();
        $orderTable = $conn->getTableName('sales_order');
        $orderGridTable = $conn->getTableName('sales_order_grid');

        $sql = "select entity_id, increment_id from $orderTable where archieve_order != 1 and created_at <= '$today'";

        if ($statuses !== false) $sql .= " and status in($statuses)";

        $sql .= ' limit 10000';

        $records = $conn->fetchAll($sql);

        $incrementIds = array_column($records, 'increment_id');
        $orderIds = array_column($records, 'entity_id');

        //exit('<pre>'.print_r($orderIds, true).'</pre>');
        //exit($sql . "\nDone");

        if (!count($incrementIds)) return false;

        $return = $this->archieveOrders($orderIds);

        if ($return) {
            //$this->_config->save($this->_lastRanDateKey, $lastRanDate, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
            $this->_helper->debug('Archieved increment_id: ' . implode(',', $incrementIds), $this->_logType, $this->_logFile);
        } else {
            $this->_helper->debug('Failed archieve increment_id, please contact your admin: ' . implode(',', $incrementIds), 'error', $this->_logFile);
        }

        return $return;
    }
}
