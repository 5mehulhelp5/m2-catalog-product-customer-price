<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice;

use Infrangible\CatalogProductCustomerPrice\Model\ProductCustomerPrice;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            ProductCustomerPrice::class,
            \Infrangible\CatalogProductCustomerPrice\Model\ResourceModel\ProductCustomerPrice::class
        );
    }

    public function addUsableFilter()
    {
        $this->getSelect()->where('main_table.used < main_table.`limit` OR main_table.`limit` IS NULL');
    }

    public function addActiveFilter()
    {
        $this->getSelect()->where(
            'main_table.active = ?',
            1
        );
    }

    public function addPriorityOrder()
    {
        $this->getSelect()->order('main_table.priority DESC');
    }

    public function addWebsiteFilter(int $websiteId, bool $includeAdmin = true)
    {
        if ($includeAdmin) {
            $this->getSelect()->where(
                'main_table.website_id IN (?)',
                [0, $websiteId]
            );
        } else {
            $this->getSelect()->where(
                'main_table.website_id = ?',
                $websiteId
            );
        }
    }
}
