<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Helper;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Helper\Cache;
use Infrangible\Core\Helper\Database;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Indexer\CacheContext;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the cache of products with added, changed and removed prices is cleaned.
 */
#[AllowMockObjectsWithoutExpectations]
class CacheTest extends TestCase
{
    /**
     * Tests the products whose prices changed since the state in the flag, and the new state in the same flag.
     *
     * @return void
     */
    public function testCleanProductCache(): void
    {
        $databaseHelper = $this->createStub(Database::class);
        $databaseHelper->method('getTableName')->willReturnArgument(0);
        $databaseHelper->method('select')->willReturn($this->createStub(Select::class));
        $databaseHelper->method('fetchAll')->willReturn(
            [
                ['product_id' => '1', 'price' => '10.00'],
                ['product_id' => '2', 'price' => '20.00'],
                ['product_id' => '2', 'price' => '18.00'],
                ['product_id' => '4', 'price' => '40.00']
            ]
        );

        $flagManager = $this->createMock(FlagManager::class);
        $flagManager->method('getFlagData')->with('test_flag')->willReturn(
            [1 => ['1|10.00'], 2 => ['2|20.00'], 3 => ['3|30.00']]
        );
        $flagManager->expects($this->once())->method('saveFlag')->with(
            'test_flag',
            [1 => ['1|10.00'], 2 => ['2|18.00', '2|20.00'], 4 => ['4|40.00']]
        );

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())->method('clean')->with(['cat_p_2', 'cat_p_3', 'cat_p_4']);

        $cacheContext = new CacheContext();

        $eventManager = $this->createMock(ManagerInterface::class);
        $eventManager->expects($this->once())->method('dispatch')->with(
            'clean_cache_by_tags',
            ['object' => $cacheContext]
        );

        $helper = new Cache($databaseHelper, $flagManager, new Variables(), $cache, $cacheContext, $eventManager);

        $helper->cleanProductCache('test_flag', 'test_table', 'product_id', ['price']);

        $this->assertSame([2, 3, 4], $cacheContext->getRegisteredEntity('cat_p'));
    }

    /**
     * Tests that nothing is cleaned without changes.
     *
     * @return void
     */
    public function testCleanProductCacheWithoutChanges(): void
    {
        $databaseHelper = $this->createStub(Database::class);
        $databaseHelper->method('select')->willReturn($this->createStub(Select::class));
        $databaseHelper->method('fetchAll')->willReturn([['product_id' => '1', 'price' => '10.00']]);

        $flagManager = $this->createStub(FlagManager::class);
        $flagManager->method('getFlagData')->willReturn([1 => ['1|10.00']]);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->never())->method('clean');

        $eventManager = $this->createMock(ManagerInterface::class);
        $eventManager->expects($this->never())->method('dispatch');

        $helper = new Cache($databaseHelper, $flagManager, new Variables(), $cache, new CacheContext(), $eventManager);

        $helper->cleanProductCache('test_flag', 'test_table', 'product_id', ['price']);
    }
}
