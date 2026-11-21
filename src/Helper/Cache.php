<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Helper;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Database;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Indexer\CacheContext;

/**
 * Cleans the cache of the products whose prices of a table changed since the last call.
 *
 * The calculated prices are part of the cached product pages and product lists. The state of the prices is kept in a
 * flag; the cache of every product whose prices were added, changed or removed is cleaned.
 */
class Cache
{
    /**
     * @param Database $databaseHelper
     * @param FlagManager $flagManager
     * @param Variables $variables
     * @param CacheInterface $cache
     * @param CacheContext $cacheContext
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        protected Database $databaseHelper,
        protected FlagManager $flagManager,
        protected Variables $variables,
        protected CacheInterface $cache,
        protected CacheContext $cacheContext,
        protected ManagerInterface $eventManager
    ) {
    }

    /**
     * Cleans the cache of the products whose prices changed since the last call with the flag.
     *
     * @param string $flagName the flag that keeps the state of the prices, one per table
     * @param string $tableName
     * @param string $productIdColumn
     * @param string[] $columns the columns of the prices that are shown
     *
     * @return void
     */
    public function cleanProductCache(
        string $flagName,
        string $tableName,
        string $productIdColumn,
        array $columns
    ): void {
        $previousPrices = $this->flagManager->getFlagData($flagName);
        $currentPrices = $this->getPrices($tableName, $productIdColumn, $columns);

        $productIds = $this->getChangedProductIds(is_array($previousPrices) ? $previousPrices : [], $currentPrices);

        if (count($productIds) > 0) {
            $tags = array_map(static fn(int $productId): string => Product::CACHE_TAG . '_' . $productId, $productIds);

            $this->cache->clean($tags);

            $this->cacheContext->registerEntities(Product::CACHE_TAG, $productIds);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        }

        $this->flagManager->saveFlag($flagName, $currentPrices);
    }

    /**
     * Returns the prices of the table by product ID, each price as text of its values.
     *
     * @param string $tableName
     * @param string $productIdColumn
     * @param string[] $columns
     *
     * @return array
     * @phpstan-return array<int, list<string>>
     */
    private function getPrices(string $tableName, string $productIdColumn, array $columns): array
    {
        $columns = array_values(array_unique(array_merge([$productIdColumn], $columns)));

        $query = $this->databaseHelper->select($this->databaseHelper->getTableName($tableName), $columns);

        $prices = [];

        foreach ($this->databaseHelper->fetchAll($query) as $row) {
            $productId = $this->variables->intValue($row[ $productIdColumn ] ?? null);

            $prices[ $productId ][] = implode(
                '|',
                array_map(static fn($value): string => is_scalar($value) ? (string)$value : '', $row)
            );
        }

        foreach ($prices as &$productPrices) {
            sort($productPrices);
        }

        return $prices;
    }

    /**
     * Returns the IDs of the products whose prices were added, changed or removed.
     *
     * @param array $previousPrices
     * @param array $currentPrices
     * @phpstan-param array<int|string, mixed> $previousPrices
     * @phpstan-param array<int, list<string>> $currentPrices
     *
     * @return int[]
     */
    private function getChangedProductIds(array $previousPrices, array $currentPrices): array
    {
        $productIds = [];

        foreach (array_unique(array_merge(array_keys($previousPrices), array_keys($currentPrices))) as $productId) {
            if (($previousPrices[ $productId ] ?? null) !== ($currentPrices[ $productId ] ?? null)) {
                $productIds[] = $this->variables->intValue($productId);
            }
        }

        return array_values(array_filter(array_unique($productIds), static fn(int $productId): bool => $productId > 0));
    }
}
