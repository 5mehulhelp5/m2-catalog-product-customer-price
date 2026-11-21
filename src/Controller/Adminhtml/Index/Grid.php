<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Controller\Adminhtml\Index;

use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPrice;

/**
 * Returns the grid of the customer prices.
 */
class Grid extends \Infrangible\BackendWidget\Controller\Backend\Object\Grid
{
    use ProductCustomerPrice;
}
