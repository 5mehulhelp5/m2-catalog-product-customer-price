<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\Tab;

use Exception;
use Infrangible\BackendWidget\Block\Grid\Tab;
use Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\PriceGridColumns;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Grid of the customer prices of a customer, in the tab of the customer form.
 */
class Grid extends Tab
{
    use PriceGridColumns;

    /**
     * The collection needs no changes.
     *
     * @param AbstractDb $collection
     * @phpstan-param AbstractDb<DataObject> $collection
     *
     * @return void
     */
    protected function prepareCollection(AbstractDb $collection): void // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
    {
        // the columns are columns of the table, the collection selects all of them
    }

    /**
     * Adds the columns.
     *
     * @return void
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addPriceColumns();
    }
}
