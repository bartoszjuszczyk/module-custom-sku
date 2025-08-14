<?php

/**
 * File: CleanCustomSkuChangelog.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */


namespace Juszczyk\CustomSku\Cron;

use Juszczyk\CustomSku\Api\CustomSkuChangelogManagementInterface;

class CleanCustomSkuChangelog
{
    /**
     * @param CustomSkuChangelogManagementInterface $customSkuChangelogPurger
     */
    public function __construct(
        protected CustomSkuChangelogManagementInterface $customSkuChangelogPurger
    ) {
    }

    /**
     * Clean custom_sku table changelog.
     *
     * @return void
     */
    public function execute(): void
    {
        $this->customSkuChangelogPurger->purge();
    }
}
