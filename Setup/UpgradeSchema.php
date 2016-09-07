<?php
/**
 * Pengo
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Pengo.com license that is
 * available through the world-wide-web at this URL:
 * 
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Pengo
 * @package    Pengo_Brand
 * @copyright  Copyright (c) 2016 Pengo (http://www.pengo.mx/)
 * @license    
 */
namespace Pengo\Brand\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'Pengo_Brand_product'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('Pengo_Brand_product')
        )->addColumn(
            'brand_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Brand ID'
        )->addColumn(
            'product_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Position'
        )->setComment(
            'Ves Brand To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
