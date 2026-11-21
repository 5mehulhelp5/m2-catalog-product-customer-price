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
 * Deletes the selected customer prices.
 */
class MassDelete extends \Infrangible\BackendWidget\Controller\Backend\Object\MassDelete
{
    use ProductCustomerPrice;

    /**
     * Returns the message after the deletion.
     *
     * @return string
     */
    protected function getObjectsDeletedMessage(): string
    {
        return __('The customer prices have been deleted.')->render();
    }
}
