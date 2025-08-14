# Magento 2 - Custom SKU Changelog Module

This module for Magento 2 introduces a new product attribute custom_sku and creates a detailed audit trail by logging
every change made to this attribute.

The module also includes a configurable, automated cron job to purge old logs, ensuring that the database table does not
grow indefinitely.

## Features

- Adds New Product Attribute: Creates a new text attribute "Custom SKU" (custom_sku) for all products.

- Changelog / Audit Trail: Automatically records every change to the custom_sku attribute in a dedicated database
  table (juszczyk_custom_sku_changelog).

- Detailed Logging: For each change, it stores the product ID, the old value, the new value, and a timestamp.

- Automatic Log Cleanup: A cron job runs periodically to clean up old log entries based on a configurable retention
  period.

- Configurable: The data retention period (in days) can be set in the Magento Admin configuration.

## Configuration

After installation, you can configure the data retention period for the changelog:

1. Navigate to Stores > Configuration > Juszczyk > Custom SKU Changelog.

2. In the General Configuration section, set the Log Retention Period (days).

3. Enter the number of days you wish to keep the logs. For example, a value of 30 will delete any log entries older than
   30 days.

4. If the value is 0 or empty, the cleanup cron job will be disabled.

5. Save the configuration.

## Usage

Once installed, the module works automatically.

Find the "Custom SKU" field in the product edit form under the "General" section.

Every time you change the value in this field and save the product, a new entry will be created in the
juszczyk_custom_sku_changelog database table.

The cron job juszczyk_customsku_clean_changelog will run according to your store's cron schedule to keep the log table
clean.
