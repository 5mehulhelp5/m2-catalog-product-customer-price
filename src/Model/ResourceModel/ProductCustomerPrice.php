<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Saves the customer prices of products in the table "catalog_product_customer_price".
 */
class ProductCustomerPrice extends AbstractDb
{
    /**
     * Initializes the table and its ID field.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('catalog_product_customer_price', 'id');
    }

    /**
     * Checks that either a price or a discount is set, empties unused values and sets the times.
     *
     * @param AbstractModel $object
     *
     * @return ProductCustomerPrice
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object): ProductCustomerPrice
    {
        parent::_beforeSave($object);

        $price = $this->getNumber($object->getData('price'));
        $discount = $this->getNumber($object->getData('discount'));

        if ($discount !== null && (int)$discount === 0) {
            $discount = null;
        }

        if (($price === null) === ($discount === null)) {
            throw new LocalizedException(__('Either a price or a discount has to be set.'));
        }

        if ($price !== null && $price < 0) {
            throw new LocalizedException(__('The price must not be negative.'));
        }

        if ($discount !== null && ($discount < 0 || $discount > 100)) {
            throw new LocalizedException(__('The discount has to be between 1 and 100 percent.'));
        }

        $limit = $this->getNumber($object->getData('limit'));

        if ($limit !== null && $limit < 0) {
            throw new LocalizedException(__('The limit must not be negative.'));
        }

        $object->setData('price', $price);
        $object->setData('discount', $discount === null ? null : (int)$discount);
        $object->setData('limit', $limit === null || (int)$limit === 0 ? null : (int)$limit);

        $now = gmdate('Y-m-d H:i:s');

        if (! $object->getId()) {
            $object->setData('created_at', $now);
        }

        $object->setData('updated_at', $now);

        return $this;
    }

    /**
     * Returns the value as number, or null if it is empty.
     *
     * @param mixed $value
     *
     * @return float|null
     * @throws LocalizedException
     */
    private function getNumber(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        if (! is_numeric($value)) {
            throw new LocalizedException(__('Please enter a number: %1', is_scalar($value) ? $value : ''));
        }

        return (float)$value;
    }
}
