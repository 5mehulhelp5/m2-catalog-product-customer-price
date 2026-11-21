<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\Tab;

use Infrangible\BackendWidget\Block\Form\Tab;
use Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\ProductCustomerPrice\PriceFormFields;
use Magento\Framework\Data\Form as DataForm;

/**
 * Form of a customer price of a customer, in the tab of the customer form.
 */
class Form extends Tab
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

        $this->addPriceFields($fieldSet);
    }
}
