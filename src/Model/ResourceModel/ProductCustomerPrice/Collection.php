<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice;

use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * The customer prices of products.
 */
class Collection extends AbstractCollection
{
    /**
     * Initializes the model and the resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            ProductCustomerPrice::class,
            \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice::class
        );
    }

    /**
     * Filters prices without limit and prices whose limit is not used up.
     *
     * @return void
     */
    public function addUsableFilter(): void
    {
        $this->getSelect()->where('main_table.`limit` IS NULL OR COALESCE(main_table.used, 0) < main_table.`limit`');
    }

    /**
     * Filters active prices.
     *
     * @return void
     */
    public function addActiveFilter(): void
    {
        $this->getSelect()->where('main_table.active = ?', 1);
    }

    /**
     * Sorts by priority, the highest first.
     *
     * @return void
     */
    public function addPriorityOrder(): void
    {
        $this->getSelect()->order('main_table.priority DESC');
    }

    /**
     * Filters prices of the website, with the prices for all websites (website ID 0) if requested.
     *
     * @param int $websiteId
     * @param bool $includeAdmin
     *
     * @return void
     */
    public function addWebsiteFilter(int $websiteId, bool $includeAdmin = true): void
    {
        if ($includeAdmin) {
            $this->getSelect()->where('main_table.website_id IN (?)', [0, $websiteId]);
        } else {
            $this->getSelect()->where('main_table.website_id = ?', $websiteId);
        }
    }
}
