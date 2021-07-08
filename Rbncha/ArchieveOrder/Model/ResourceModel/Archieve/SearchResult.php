<?php

namespace Rbncha\ArchieveOrder\Model\ResourceModel\Archieve;
use Magento\Framework\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as ParentSearchResult;

class SearchResult extends ParentSearchResult
{
	public function _construct()
	{
		parent::_construct();
	}

	protected function _renderFiltersBefore()
    {
        $this->getSelect()->where('main_table.archieve_order = 1');
        parent::_renderFiltersBefore();
    }
}