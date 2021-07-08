<?php
/**
 * @author Rubin Shrestha
 */

namespace Rbncha\ArchieveOrder\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Phrase;


class Archieve extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}

    public function _construct()
    {
        $this->_init('sales_order_grid', 'entity_id');
    }
}