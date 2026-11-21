<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Grid of the customer prices.
 */
class Grid extends \Infrangible\BackendWidget\Block\Grid
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
        $this->addCustomerNameColumn('customer_id', __('Customer')->render());
        $this->addPriceColumns();
    }

    /**
     * The grid has no hidden fields.
     *
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
