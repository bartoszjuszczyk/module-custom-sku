<?php

/**
 * File: UpdateCustomSkuChangelog.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */

namespace Juszczyk\CustomSku\Observer;

use Exception;
use Juszczyk\CustomSku\Api\CustomSkuChangelogManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class UpdateCustomSkuChangelog implements ObserverInterface
{
    private const string LOG_PREFIX = '[Juszczyk_CustomSku] ';
    private const string ATTRIBUTE_CODE = 'custom_sku';

    /**
     * @param CustomSkuChangelogManagementInterface $customSkuChangelogManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CustomSkuChangelogManagementInterface $customSkuChangelogManagement,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        try {
            if (! $product->getId() || ! $product->dataHasChangedFor('custom_sku')) {
                return $this;
            }

            $oldValue = $product->getOrigData(self::ATTRIBUTE_CODE);
            $newValue = $product->getData(self::ATTRIBUTE_CODE);

            $this->customSkuChangelogManagement->recordCustomSkuChange((int) $product->getId(), $oldValue, $newValue);
        } catch (Exception $e) {
            $context = ['product_id' => $product->getId() ?? null];
            $this->logger->error(self::LOG_PREFIX . $e->getMessage(), $context);
            $this->logger->error(self::LOG_PREFIX . $e->getTraceAsString());
        }
        return $this;
    }
}
