<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Test\Unit\Model\Calculation;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPrice;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\Website;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the availability of a customer price for the customer, the website and the limit.
 */
#[AllowMockObjectsWithoutExpectations]
class ProductCustomerPriceTest extends TestCase
{
    /**
     * Tests that the option code contains the ID of the customer price, and the code does not contain the customer.
     *
     * @return void
     */
    public function testCodes(): void
    {
        $calculation = $this->createCalculation(5, 0, []);
        $calculation->setCustomerPriceId(7);

        $otherCalculation = $this->createCalculation(6, 0, []);
        $otherCalculation->setCustomerPriceId(8);

        $this->assertSame('customer_price_7', $calculation->getQuoteItemOptionCode());
        $this->assertSame($calculation->getCode(), $otherCalculation->getCode());
        $this->assertStringStartsWith('pcp_', $calculation->getCode());
    }

    /**
     * Tests the price for the product of the price only.
     *
     * @return void
     */
    public function testHasProductCalculation(): void
    {
        $calculation = $this->createCalculation(5, 0, []);

        $this->assertTrue($calculation->hasProductCalculation($this->createProduct('10')));
        $this->assertFalse($calculation->hasProductCalculation($this->createProduct('11')));
    }

    /**
     * Tests the availability for the customer of the price, in its website.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testIsAvailableForProduct(): void
    {
        $this->assertTrue($this->createCalculation(5, 0, [])->isAvailableForProduct());
        $this->assertFalse($this->createCalculation(6, 0, [])->isAvailableForProduct());
        $this->assertFalse($this->createCalculation(5, 0, [], false)->isAvailableForProduct());
        $this->assertTrue($this->createCalculation(5, 1, [])->isAvailableForProduct());
        $this->assertFalse($this->createCalculation(5, 2, [])->isAvailableForProduct());
    }

    /**
     * Tests the limit with the quantity of the product in the cart.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testLimit(): void
    {
        $calculation = $this->createCalculation(5, 0, [$this->createItem('10', 2.), $this->createItem('11', 5.)]);
        $calculation->setLimit(3);

        $this->assertTrue($calculation->isAvailableForProduct());
        $this->assertTrue($calculation->hasProductQty(1.));
        $this->assertFalse($calculation->hasProductQty(2.));

        $calculation->setLimit(2);

        $this->assertFalse($calculation->isAvailableForProduct());
    }

    /**
     * Tests the limit of quote items with the items that already got the price, without counting the item twice.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testQuoteItemLimit(): void
    {
        $calculation = $this->createCalculation(5, 0, []);
        $calculation->setLimit(3);

        $item = $this->createItem('10', 2.);
        $calculatedItem = $this->createItem('10', 1.);

        $this->assertTrue($calculation->isAvailableForQuoteItem($item, [$calculatedItem, $item]));
        $this->assertFalse($calculation->hasQuoteItemQty($item, [$calculatedItem, $this->createItem('10', 1.)]));
    }

    /**
     * Returns the calculation of a price of customer 5 for product 10 in website 1.
     *
     * @param int $customerId the logged-in customer
     * @param int $websiteId the website of the price, 0 for all websites
     * @param Item[] $cartItems
     * @param bool $loggedIn
     *
     * @return ProductCustomerPrice
     */
    private function createCalculation(
        int $customerId,
        int $websiteId,
        array $cartItems,
        bool $loggedIn = true
    ): ProductCustomerPrice {
        $customerSession = $this->createStub(CustomerSession::class);
        $customerSession->method('isLoggedIn')->willReturn($loggedIn);
        $customerSession->method('getCustomerId')->willReturn((string)$customerId);

        $quote = $this->createStub(Quote::class);
        $quote->method('getAllVisibleItems')->willReturn($cartItems);

        $checkoutSession = $this->createStub(CheckoutSession::class);
        $checkoutSession->method('getQuote')->willReturn($quote);

        $website = $this->createStub(Website::class);
        $website->method('getId')->willReturn('1');

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getWebsite')->willReturn($website);

        $calculation = new ProductCustomerPrice(
            $this->createStub(SimpleFactory::class),
            $this->createStub(AmountFactory::class),
            $customerSession,
            $checkoutSession,
            new Json(),
            $this->createStub(Data::class),
            $storeHelper,
            new Variables()
        );

        $calculation->setCustomerId(5);
        $calculation->setProductId(10);
        $calculation->setPrice(9.5);
        $calculation->setWebsiteId($websiteId);

        return $calculation;
    }

    /**
     * Returns a product.
     *
     * @param string $productId
     *
     * @return Product
     */
    private function createProduct(string $productId): Product
    {
        $product = $this->createStub(Product::class);
        $product->method('getId')->willReturn($productId);

        return $product;
    }

    /**
     * Returns a quote item of the product.
     *
     * @param string $productId
     * @param float $qty
     *
     * @return Item
     */
    private function createItem(string $productId, float $qty): Item
    {
        $item = $this->createStub(Item::class);
        $item->method('getProduct')->willReturn($this->createProduct($productId));
        $item->method('getQty')->willReturn($qty);

        return $item;
    }
}
