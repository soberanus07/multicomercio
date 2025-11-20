<?php
/**
 * Chilean Regions
 *
 * @category   ChilexpressOficial
 * @package    ChilexpressOficial_Chilexpress
 */

namespace ChilexpressOficial\Chilexpress\Setup;

use Magento\Directory\Helper\Data;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * Directory data
     *
     * @var Data
     */
    protected $directoryData;

    /**
     * InstallData constructor.
     *
     * @param Data $directoryData
     */
    public function __construct(Data $directoryData)
    {
        $this->directoryData = $directoryData;
    }

    /**
     * Installs data.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Fill table directory/country_region
         * Fill table directory/country_region_name for en_US locale
         */

        $data = [
            ['CL', 'R1', 'TARAPACA'],
            ['CL', 'R2', 'ANTOFAGASTA'],
            ['CL', 'R3', 'ATACAMA'],
            ['CL', 'R4', 'COQUIMBO'],
            ['CL', 'R5', 'VALPARAISO'],
            ['CL', 'R6', 'LIBERTADOR GRAL BERNARDO O HIGGINS'],
            ['CL', 'R7', 'MAULE'],
            ['CL', 'R8', 'BIOBIO'],
            ['CL', 'R9', 'ARAUCANIA'],
            ['CL', 'RM', 'METROPOLITANA DE SANTIAGO'],
            ['CL', 'R10', 'LOS LAGOS'],
            ['CL', 'R11', 'AISEN DEL GRAL C IBANEZ DEL CAMPO'],
            ['CL', 'R12', 'MAGALLANES Y LA ANTARTICA CHILENA'],
            ['CL', 'R14', 'LOS RIOS'],
            ['CL', 'R15', 'ARICA Y PARINACOTA'],
            ['CL', 'R16', 'NUBLE'],
        ];

        foreach ($data as $row) {
            $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
            $setup->getConnection()->insert($setup->getTable('directory_country_region'), $bind);
            $regionId = $setup->getConnection()->lastInsertId($setup->getTable('directory_country_region'));

            $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
            $setup->getConnection()->insert($setup->getTable('directory_country_region_name'), $bind);
        }
    }

}