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
 * Saves a customer price of a customer.
 */
class Save extends \Infrangible\BackendWidget\Controller\Backend\Object\Tab\Save
{
    use ProductCustomerPrice;
    use ProductCustomerPriceTab;

    /**
     * Returns the message after the creation.
     *
     * @return string
     */
    protected function getObjectCreatedMessage(): string
    {
        return __('The customer price has been created.')->render();
    }

    /**
     * Returns the message after the update.
     *
     * @return string
     */
    protected function getObjectUpdatedMessage(): string
    {
        return __('The customer price has been saved.')->render();
    }
}
