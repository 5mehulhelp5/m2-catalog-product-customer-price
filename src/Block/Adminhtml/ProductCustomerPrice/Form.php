<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice;

use Magento\Framework\Data\Form as DataForm;

/**
 * Form of a customer price.
 */
class Form extends \Infrangible\BackendWidget\Block\Form
{
    use PriceFormFields;

    /**
     * Adds the fields.
     *
     * @param DataForm $form
     *
     * @return void
     */
    protected function prepareFields(DataForm $form): void
    {
        $fieldSet = $form->addFieldset('general', ['legend' => __('General')]);

        $this->addCustomerNameField($fieldSet, 'customer_id', __('Customer')->render(), true);
        $this->addPriceFields($fieldSet);
    }
}
