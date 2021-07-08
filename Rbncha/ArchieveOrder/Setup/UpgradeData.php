<?php
namespace Rbncha\ArchieveOrder\Setup;

use \Magento\Framework\Setup\UpgradeDataInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Framework\DB\Ddl\Table;
/**
* @codeCoverageIgnore
*/
class UpgradeData implements UpgradeDataInterface
{    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;
    
    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     * @param \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        
        $salesInstaller->addAttribute(
            'order',
            'archieve_order',
	            ['type' => Table::TYPE_BOOLEAN,
	            'nullable' => false, 
	            'default' => '0',
	            'grid' => true]
        );

        $setup->endSetup();
    }
}
