<?php

/**
 * File: CustomSkuChangelogPurgerInterface.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */

namespace Juszczyk\CustomSku\Api;

interface CustomSkuChangelogManagementInterface
{
    /**
     * Purge old rows in custom_sku product attribute changelog table.
     *
     * @return void
     */
    public function purge(): void;

    /**
     * Record custom_sku product attribute change in changelog table.
     *
     * @param int $productId
     * @param string|null $oldValue
     * @param string|null $newValue
     * @return void
     */
    public function recordCustomSkuChange(int $productId, ?string $oldValue, ?string $newValue): void;
}
