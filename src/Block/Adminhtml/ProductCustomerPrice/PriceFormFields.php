<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice;

use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * The fields of a customer price without the customer, for the forms of all prices and of the prices of a customer.
 *
 * @mixin \Infrangible\BackendWidget\Block\Form
 */
trait PriceFormFields
{
    /**
     * Adds the fields of the price to the form.
     *
     * @param Fieldset $fieldSet
     *
     * @return void
     */
    protected function addPriceFields(Fieldset $fieldSet): void
    {
        $this->addProductNameField($fieldSet, 'product_id', __('Product')->render(), true);
        $this->addPriceField($fieldSet, 'price', __('Price')->render());
        $this->addDiscountField($fieldSet, 'discount', __('Discount')->render());
        $this->addIntegerField($fieldSet, 'limit', __('Limit')->render());
        $this->addIntegerField($fieldSet, 'priority', __('Priority')->render());
        $this->addWebsiteSelectField($fieldSet, 'website_id');
        $this->addYesNoWithDefaultField($fieldSet, 'active', __('Active')->render(), 1);
    }
}
