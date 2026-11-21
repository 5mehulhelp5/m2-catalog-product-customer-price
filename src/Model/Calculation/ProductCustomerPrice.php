<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Model\Calculation;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Base;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationDataInterface;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Quote\Model\Quote\Item;

/**
 * The calculation of a customer price: available for the customer of the price, in the website of the price, as long
 * as the quantity in the cart does not exceed the limit that is left.
 */
class ProductCustomerPrice extends Base implements CalculationDataInterface
{
    /** Prefix of the codes of the quote item options, followed by the ID of the customer price. */
    public const QUOTE_ITEM_OPTION_CODE_PREFIX = 'customer_price_';

    /** @var int */
    private int $customerId = 0;

    /** @var int */
    private int $productId = 0;

    /** @var float|null */
    private ?float $price = null;

    /** @var int|null */
    private ?int $discount = null;

    /** @var int|null */
    private ?int $limit = null;

    /** @var int */
    private int $websiteId = 0;

    /** @var int */
    private int $customerPriceId = 0;

    /**
     * @param SimpleFactory $pricesFactory
     * @param AmountFactory $amountFactory
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Json $json
     * @param Data $priceCalculationHelper
     * @param Stores $storeHelper
     * @param Variables $variables
     */
    public function __construct(
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory,
        protected CustomerSession $customerSession,
        protected CheckoutSession $checkoutSession,
        protected Json $json,
        protected Data $priceCalculationHelper,
        protected Stores $storeHelper,
        protected Variables $variables
    ) {
        parent::__construct($pricesFactory, $amountFactory);
    }

    /**
     * Returns the code, which is the same for all customers with the same price of a product.
     *
     * The code is part of the cached product pages, so it must not contain the customer.
     *
     * @return string
     */
    public function getCode(): string
    {
        $code = parent::getCode();

        if ($code === '') {
            $code = sprintf(
                'pcp_%s',
                hash(
                    'sha256',
                    $this->json->encode(
                        [
                            'product_id' => $this->getProductId(),
                            'price'      => $this->getPrice(),
                            'discount'   => $this->getDiscount(),
                            'priority'   => $this->getPriority(),
                            'website_id' => $this->getWebsiteId()
                        ]
                    ) ?? ''
                )
            );

            $this->setCode($code);
        }

        return $code;
    }

    /**
     * Returns the ID of the customer.
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Sets the ID of the customer.
     *
     * @param int $customerId
     *
     * @return void
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * Returns the ID of the product.
     *
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * Sets the ID of the product.
     *
     * @param int $productId
     *
     * @return void
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * Returns the fixed price, or null for a discount.
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Sets the fixed price.
     *
     * @param float|null $price
     *
     * @return void
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * Returns the discount in percent, or null for a fixed price.
     *
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    /**
     * Sets the discount in percent.
     *
     * @param int|null $discount
     *
     * @return void
     */
    public function setDiscount(?int $discount): void
    {
        $this->discount = $discount;
    }

    /**
     * Returns the quantity that is left of the limit, or null without limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Sets the quantity that is left of the limit.
     *
     * @param int|null $limit
     *
     * @return void
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Returns the ID of the website, 0 for all websites.
     *
     * @return int
     */
    public function getWebsiteId(): int
    {
        return $this->websiteId;
    }

    /**
     * Sets the ID of the website.
     *
     * @param int $websiteId
     *
     * @return void
     */
    public function setWebsiteId(int $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * Returns the ID of the customer price.
     *
     * @return int
     */
    public function getCustomerPriceId(): int
    {
        return $this->customerPriceId;
    }

    /**
     * Sets the ID of the customer price.
     *
     * @param int $customerPriceId
     *
     * @return void
     */
    public function setCustomerPriceId(int $customerPriceId): void
    {
        $this->customerPriceId = $customerPriceId;
    }

    /**
     * Returns the code of the quote item option: the prefix and the ID of the customer price.
     *
     * @return string
     */
    public function getQuoteItemOptionCode(): string
    {
        return self::QUOTE_ITEM_OPTION_CODE_PREFIX . $this->getCustomerPriceId();
    }

    /**
     * Checks if the price is for the product.
     *
     * @param Product $product
     *
     * @return bool
     */
    public function hasProductCalculation(Product $product): bool
    {
        return $this->variables->intValue($product->getId()) === $this->getProductId();
    }

    /**
     * Returns the calculated prices of the product.
     *
     * @param Product $product
     *
     * @return PricesInterface
     * @throws LocalizedException
     */
    public function getProductPrices(Product $product): PricesInterface
    {
        return $this->priceCalculationHelper->calculatePrices($product, $this);
    }

    /**
     * Customer prices have no tier prices.
     *
     * @param Product $product
     *
     * @return PricesInterface[]
     */
    public function getProductTierPrices(Product $product): array
    {
        return [];
    }

    /**
     * Checks if the price is available for the logged-in customer in the website, with a limit left in the cart.
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isAvailableForProduct(): bool
    {
        if (! $this->isCustomer() || ! $this->isWebsite()) {
            return false;
        }

        return $this->getLimit() === null || $this->getCartQty() < $this->getLimit();
    }

    /**
     * Checks if the quantity can be added to the quantity of the product in the cart within the limit.
     *
     * @param float $qty
     *
     * @return bool
     * @throws LocalizedException
     */
    public function hasProductQty(float $qty): bool
    {
        return $this->getLimit() === null || $this->getCartQty() + $qty <= $this->getLimit();
    }

    /**
     * Checks if the price is available for the customer and the website, and the quantity of the item fits.
     *
     * The quantity has to fit into the limit together with the items that already got the price.
     *
     * @param Item $item
     * @param Item[] $calculatedItems
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isAvailableForQuoteItem(Item $item, array $calculatedItems): bool
    {
        return $this->isCustomer() && $this->isWebsite() && $this->hasQuoteItemQty($item, $calculatedItems);
    }

    /**
     * Checks if the quantity of the item fits into the limit together with the items that already got the price.
     *
     * @param Item $item
     * @param Item[] $calculatedItems
     *
     * @return bool
     */
    public function hasQuoteItemQty(Item $item, array $calculatedItems): bool
    {
        if ($this->getLimit() === null) {
            return true;
        }

        return $this->getItemsQty($calculatedItems, $item) + (float)$item->getQty() <= $this->getLimit();
    }

    /**
     * Checks if the logged-in customer is the customer of the price.
     *
     * @return bool
     */
    private function isCustomer(): bool
    {
        if (! $this->customerSession->isLoggedIn()) {
            return false;
        }

        return $this->variables->intValue($this->customerSession->getCustomerId()) === $this->getCustomerId();
    }

    /**
     * Checks if the price is for all websites or for the current website.
     *
     * @return bool
     * @throws LocalizedException
     */
    private function isWebsite(): bool
    {
        if ($this->getWebsiteId() === 0) {
            return true;
        }

        return $this->variables->intValue($this->storeHelper->getWebsite()->getId()) === $this->getWebsiteId();
    }

    /**
     * Returns the quantity of the product in the cart.
     *
     * @return float
     * @throws LocalizedException
     */
    private function getCartQty(): float
    {
        return $this->getItemsQty($this->checkoutSession->getQuote()->getAllVisibleItems());
    }

    /**
     * Returns the quantity of the product in the items, without the excluded item.
     *
     * @param Item[] $items
     * @param Item|null $excludedItem
     *
     * @return float
     */
    private function getItemsQty(array $items, ?Item $excludedItem = null): float
    {
        $qty = 0.;

        foreach ($items as $item) {
            if ($item === $excludedItem || $item->isDeleted()) {
                continue;
            }

            if ($this->variables->intValue($item->getProduct()->getId()) === $this->getProductId()) {
                $qty += (float)$item->getQty();
            }
        }

        return $qty;
    }
}
