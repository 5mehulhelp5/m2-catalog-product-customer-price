<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Traits;

/**
 * The settings of the customer prices in the tab of the customer form.
 */
trait ProductCustomerPriceTab
{
    /**
     * Returns the field of the customer prices with the ID of the customer.
     *
     * @return string
     */
    protected function getParentObjectKey(): string
    {
        return 'customer_id';
    }

    /**
     * Returns the request parameter with the ID of the customer.
     *
     * @return string
     */
    protected function getParentObjectValueKey(): string
    {
        return 'id';
    }
}
