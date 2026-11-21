<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice;

use Exception;

/**
 * The columns of a customer price without the customer, for the grids of all prices and of the prices of a customer.
 *
 * @mixin \Infrangible\BackendWidget\Block\Grid
 */
trait PriceGridColumns
{
    /**
     * Adds the columns of the price to the grid.
     *
     * @return void
     * @throws Exception
     */
    protected function addPriceColumns(): void
    {
        $this->addProductNameColumn('product_id', __('Product')->render());
        $this->addNumberColumn('price', __('Price')->render());
        $this->addNumberColumn('discount', __('Discount')->render());
        $this->addNumberColumn('limit', __('Limit')->render());
        $this->addNumberColumn('priority', __('Priority')->render());
        $this->addWebsiteNameColumn('website_id');
        $this->addNumberColumn('used', __('Used')->render());
        $this->addYesNoColumn('active', __('Active')->render());
    }
}
