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
 * Deletes a customer price of a customer.
 */
class Delete extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Delete
{
    use ProductCustomerPrice;
    use ProductCustomerPriceTab;

    /**
     * Returns the message after the deletion.
     *
     * @return string
     */
    protected function getObjectDeletedMessage(): string
    {
        return __('The customer price has been deleted.')->render();
    }
}
