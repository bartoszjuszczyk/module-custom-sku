<?php

/**
 * File: Config.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */

namespace Juszczyk\CustomSku\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    private const string XML_PATH_CUSTOM_SKU_GENERAL_RETENTION_DAYS = 'custom_sku/general/retention_days';

    /**
     * Get retention days for custom_sku changelog table.
     *
     * @return int
     */
    public function getRetentionDays(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_SKU_GENERAL_RETENTION_DAYS
        );
    }
}
