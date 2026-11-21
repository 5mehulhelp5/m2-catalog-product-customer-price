<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Controller\Adminhtml\Tab;

use Infrangible\CatalogProductCustomerPrice\Traits\ProductCustomerPrice;

/**
 * Shows the form of a new customer price of a customer.
 */
class Add extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Add
{
    use ProductCustomerPrice;
}
