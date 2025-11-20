<?php
/**
 * Chilean Regions
 *
 * @category   ChilexpressOficial
 * @package    ChilexpressOficial_Chilexpress
 */

namespace ChilexpressOficial\Chilexpress\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $setup->getConnection()->delete(
            $setup->getTable('directory_country_region_name'),
            ['region_id IN (SELECT region_id FROM directory_country_region WHERE country_id = ?)' => 'CL']
        );

        $setup->getConnection()->delete(
            $setup->getTable('directory_country_region'),
            ['country_id = ?' => 'CL']
        );

        $setup->endSetup();
    }

}