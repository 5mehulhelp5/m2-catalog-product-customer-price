<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Observer;

use ArrayIterator;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPrice as Calculation;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPriceFactory;
use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice\Collection;
use Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice\CollectionFactory;
use Infrangible\CatalogProductCustomerPrice\Observer\CatalogProductPriceCalculation;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Infrangible\Core\Helper\Stores;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Store\Model\Website;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the customer prices are added as calculations.
 */
#[AllowMockObjectsWithoutExpectations]
class CatalogProductPriceCalculationTest extends TestCase
{
    /**
     * Tests the values of the calculations: a fixed price, and a discount with the limit that is left.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testExecute(): void
    {
        $fixedPrice = $this->createCustomerPrice(
            ['id' => '3', 'customer_id' => '5', 'product_id' => '10', 'price' => '9.50', 'discount' => null,
             'limit' => null, 'used' => '0', 'priority' => '100', 'website_id' => '0']
        );
        $discount = $this->createCustomerPrice(
            ['id' => '4', 'customer_id' => '6', 'product_id' => '11', 'price' => null, 'discount' => '15',
             'limit' => '5', 'used' => '2', 'priority' => '50', 'website_id' => '1']
        );

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addUsableFilter');
        $collection->expects($this->once())->method('addActiveFilter');
        $collection->expects($this->once())->method('addWebsiteFilter')->with(1);
        $collection->method('getIterator')->willReturn(new ArrayIterator([$fixedPrice, $discount]));

        $collectionFactory = $this->createStub(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);

        $first = $this->createCalculation();
        $second = $this->createCalculation();

        $calculationFactory = $this->createStub(ProductCustomerPriceFactory::class);
        $calculationFactory->method('create')->willReturn($first, $second);

        $website = $this->createStub(Website::class);
        $website->method('getId')->willReturn('1');

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getWebsite')->willReturn($website);

        $calculations = new Calculations();

        $observer = new CatalogProductPriceCalculation(
            $calculationFactory,
            $collectionFactory,
            new Variables(),
            $storeHelper
        );

        $observer->execute(new Observer(['calculations' => $calculations]));

        $this->assertSame([$first, $second], $calculations->getCalculations());

        $this->assertSame('customer_price_3', $first->getQuoteItemOptionCode());
        $this->assertSame(9.5, $first->getPrice());
        $this->assertNull($first->getDiscount());
        $this->assertNull($first->getLimit());
        $this->assertSame(100, $first->getPriority());
        $this->assertSame(6, $second->getCustomerId());
        $this->assertNull($second->getPrice());
        $this->assertSame(15, $second->getDiscount());
        $this->assertSame(3, $second->getLimit());
        $this->assertSame(1, $second->getWebsiteId());
    }

    /**
     * Returns a calculation with stubs of its dependencies.
     *
     * @return Calculation
     */
    private function createCalculation(): Calculation
    {
        return new Calculation(
            $this->createStub(SimpleFactory::class),
            $this->createStub(AmountFactory::class),
            $this->createStub(CustomerSession::class),
            $this->createStub(CheckoutSession::class),
            new Json(),
            $this->createStub(Data::class),
            $this->createStub(Stores::class),
            new Variables()
        );
    }

    /**
     * Returns a customer price with the data.
     *
     * @param array $data
     * @phpstan-param array<string, string|null> $data
     *
     * @return ProductCustomerPrice
     */
    private function createCustomerPrice(array $data): ProductCustomerPrice
    {
        $customerPrice = $this->getMockBuilder(ProductCustomerPrice::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $customerPrice->setData($data);

        return $customerPrice;
    }
}
