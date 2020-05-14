<?php

namespace Verdikt\CatalogFile\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('verdikt_catalog_files')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('verdikt_catalog_files')
			)
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'File ID'
				)
				->addColumn(
					'website_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
                        'nullable' => false
                    ],
					'Website ID'
				)
				->addColumn(
					'file',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Catalog PDF File'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
                );
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}