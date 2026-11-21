<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPrice;
use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPriceTab;

/**
 * Returns the grid of the customer prices of a customer.
 */
class Grid extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Grid
{
    use ProductCustomerPrice;
    use ProductCustomerPriceTab;
}
