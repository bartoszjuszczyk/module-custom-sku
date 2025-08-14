<?php

/**
 * File: AddCustomSkuProductAttributePatch.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */

namespace Juszczyk\CustomSku\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomSkuProductAttributePatch implements DataPatchInterface
{
    private const string ATTRIBUTE_CODE = 'custom_sku';
    private const string ATTRIBUTE_LABEL = 'Custom SKU';
    private const string ATTRIBUTE_GROUP = 'General';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'varchar',
                'label' => self::ATTRIBUTE_LABEL,
                'input' => 'text',
                'group' => self::ATTRIBUTE_GROUP,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
                'unique' => false,
                'system' => false,
                'user_defined' => true,
                'default' => '',
                'comparable' => false,
                'filterable' => false,
                'searchable' => false,
                'visible' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
