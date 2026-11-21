<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Observer;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Helper\Cache;
use Infrangible\CatalogProductCustomerPrice\Helper\Data;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice as PriceResource;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPriceFactory as ResourceFactory;
use Infrangible\CatalogProductCustomerPrice\Observer\SalesOrderPlaceAfter;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests that ordered quantities are added to the used quantity of the customer prices.
 */
#[AllowMockObjectsWithoutExpectations]
class SalesOrderPlaceAfterTest extends TestCase
{
    /**
     * Tests the ordered quantities of the items with a customer price, also with the codes before version 4.0, and
     * that items of other calculations and customer prices that do not exist any more are skipped.
     *
     * @return void
     */
    public function testExecute(): void
    {
        $saved = [];

        $resource = $this->createStub(PriceResource::class);
        $resource->method('load')->willReturnCallback(
            static function (AbstractModel $object, mixed $id) use ($resource): PriceResource {
                $stored = [12 => ['id' => '12', 'used' => '1'], 14 => ['id' => '14', 'used' => null]];

                if (is_int($id) && isset($stored[ $id ])) {
                    $object->setData($stored[ $id ]);
                }

                return $resource;
            }
        );
        $resource->method('save')->willReturnCallback(
            static function (AbstractModel $object) use (&$saved, $resource): PriceResource {
                $saved[ $object->getId() ] = $object->getData('used');

                return $resource;
            }
        );

        $resourceFactory = $this->createStub(ResourceFactory::class);
        $resourceFactory->method('create')->willReturn($resource);

        $priceFactory = $this->createStub(ProductCustomerPriceFactory::class);
        // one customer price per ordered customer price ID: 12, 14 and 13
        $priceFactory->method('create')->willReturn(
            $this->createCustomerPrice(),
            $this->createCustomerPrice(),
            $this->createCustomerPrice()
        );

        $order = $this->createStub(Order::class);
        $order->method('getAllVisibleItems')->willReturn(
            [
                $this->createItem('customer_price_12', 2.),
                $this->createItem('12', 1.),
                $this->createItem('14', 3.),
                $this->createItem('customer_price_13', 1.),
                $this->createItem('pcb_5f2b', 1.),
                $this->createItem(null, 1.)
            ]
        );

        $observer = new SalesOrderPlaceAfter(
            new Arrays(new Variables()),
            new Data($this->createStub(Cache::class)),
            $priceFactory,
            $resourceFactory,
            $this->createStub(LoggerInterface::class)
        );

        $observer->execute(new Observer(['order' => $order]));

        $this->assertSame(['12' => 4, '14' => 3], $saved);
    }

    /**
     * Tests that the event without an order is skipped.
     *
     * @return void
     */
    public function testExecuteWithoutOrder(): void
    {
        $resourceFactory = $this->createMock(ResourceFactory::class);
        $resourceFactory->expects($this->never())->method('create');

        $observer = new SalesOrderPlaceAfter(
            new Arrays(new Variables()),
            new Data($this->createStub(Cache::class)),
            $this->createStub(ProductCustomerPriceFactory::class),
            $resourceFactory,
            $this->createStub(LoggerInterface::class)
        );

        $observer->execute(new Observer(['order' => new DataObject()]));
    }

    /**
     * Returns a customer price without its constructor, whose data the stub of the resource model sets.
     *
     * @return ProductCustomerPrice
     */
    private function createCustomerPrice(): ProductCustomerPrice
    {
        return $this->getMockBuilder(ProductCustomerPrice::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
    }

    /**
     * Returns an order item with the code of the calculation.
     *
     * @param string|null $code
     * @param float $qty
     *
     * @return Item
     */
    private function createItem(?string $code, float $qty): Item
    {
        $item = $this->createStub(Item::class);
        $item->method('getProductOptions')->willReturn($code === null ? [] : ['price_calculation' => $code]);
        $item->method('getQtyOrdered')->willReturn($qty);

        return $item;
    }
}
