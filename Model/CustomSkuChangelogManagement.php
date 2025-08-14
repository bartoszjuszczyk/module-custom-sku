<?php

/**
 * File: CustomSkuChangelogManagement.php
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 * @copyright Copyright (C) 2025 Bartosz Juszczyk
 */

namespace Juszczyk\CustomSku\Model;

use DateTime;
use Exception;
use Juszczyk\CustomSku\Api\CustomSkuChangelogManagementInterface;
use Juszczyk\CustomSku\Helper\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

class CustomSkuChangelogManagement implements CustomSkuChangelogManagementInterface
{
    private const string TABLE_NAME = 'juszczyk_custom_sku_changelog';
    private const string LOG_PREFIX = '[Juszczyk_CustomSku] ';

    /**
     * @var AdapterInterface
     */
    protected AdapterInterface $connection;

    /**
     * @param Config $config
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly Config $config,
        private readonly LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function purge(): void
    {
        $retentionDays = $this->getRetentionDays();
        if (! $this->isRetentionDaysValid($retentionDays)) {
            $this->logger->info(self::LOG_PREFIX . "Retention days not set or less than 0.");
            return;
        }
        $this->executeTransaction(function () use ($retentionDays) {
            $this->deleteOldRecords($retentionDays);
        });
    }

    /**
     * @inheritDoc
     */
    public function recordCustomSkuChange(int $productId, ?string $oldValue, ?string $newValue): void
    {
        $this->executeTransaction(function () use ($productId, $oldValue, $newValue) {
            $this->insertNewRecord($productId, $oldValue, $newValue);
        });
    }

    /**
     * Check if retention days are greater than zero.
     *
     * @param int $retentionDays
     * @return bool
     */
    protected function isRetentionDaysValid(int $retentionDays): bool
    {
        return $retentionDays > 0;
    }

    /**
     * Insert new record to custom_sku product attribute changelog table.
     *
     * @param int $productId
     * @param string|null $oldValue
     * @param string|null $newValue
     * @return void
     */
    protected function insertNewRecord(int $productId, ?string $oldValue, ?string $newValue): void
    {
        $this->connection->insert(
            $this->connection->getTableName(self::TABLE_NAME),
            [
                'product_id' => $productId,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]
        );
    }

    /**
     * Delete old records from change_sku product attribute changelog table.
     *
     * @param int $retentionDays
     * @return void
     */
    protected function deleteOldRecords(int $retentionDays): void
    {
        $cutoffDate = $this->getCutoffDate($retentionDays)->format('Y-m-d H:i:s');
        $tableName = $this->connection->getTableName(self::TABLE_NAME);

        $this->connection->delete(
            $tableName,
            ['changed_at < ?' => $cutoffDate]
        );
    }

    /**
     * Get retention days.
     *
     * @return int
     */
    private function getRetentionDays(): int
    {
        return $this->config->getRetentionDays();
    }

    /**
     * Get cutoff date.
     *
     * @param int $retentionDays
     * @return DateTime
     */
    private function getCutoffDate(int $retentionDays): DateTime
    {
        return new DateTime("-$retentionDays days");
    }

    /**
     * Execute DB transaction.
     *
     * @param callable $function
     * @return void
     */
    private function executeTransaction(callable $function): void
    {
        try {
            $this->connection->beginTransaction();
            $function();
            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            $this->logError($e->getMessage(), $e->getTraceAsString());
        }
    }

    /**
     * Log error.
     *
     * @param string $message
     * @param string $trace
     * @return void
     */
    private function logError(string $message, string $trace): void
    {
        $this->logger->error(self::LOG_PREFIX . $message);
        $this->logger->error(self::LOG_PREFIX . $trace);
    }
}
