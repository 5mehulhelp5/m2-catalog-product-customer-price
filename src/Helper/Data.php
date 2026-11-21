<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Helper;

use Infrangible\CatalogProductCustomerPrice\Model\Calculation\ProductCustomerPrice;

/**
 * Cleans the cache of the products whose customer prices changed, and reads the customer prices of order items.
 */
class Data
{
    /**
     * @param Cache $cacheHelper
     */
    public function __construct(protected Cache $cacheHelper)
    {
    }

    /**
     * Cleans the cache of the products whose customer prices changed.
     *
     * @return void
     */
    public function cleanProductCache(): void
    {
        $this->cacheHelper->cleanProductCache(
            'product_customer_price',
            'catalog_product_customer_price',
            'product_id',
            ['price', 'discount', 'priority', 'website_id', 'active', 'limit', 'used']
        );
    }

    /**
     * Returns the ID of the customer price of a quote item option code, or null if it belongs to another calculation.
     *
     * Before version 4.0, the code was the ID alone.
     *
     * @param mixed $code
     *
     * @return int|null
     */
    public function getCustomerPriceIdByOptionCode(mixed $code): ?int
    {
        if (! is_scalar($code)) {
            return null;
        }

        $pattern = '/^(?:' . ProductCustomerPrice::QUOTE_ITEM_OPTION_CODE_PREFIX . ')?([1-9]\d*)$/';

        return preg_match($pattern, (string)$code, $matches) === 1 ? (int)$matches[ 1 ] : null;
    }
}
