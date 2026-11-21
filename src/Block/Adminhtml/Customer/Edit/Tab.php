<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Block\Adminhtml\Customer\Edit;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * The tab "Customer Prices" in the form of a customer, which loads the grid of the prices of the customer.
 */
class Tab extends Template implements TabInterface
{
    /**
     * @param Context $context
     * @param Registry $registryHelper
     * @param Variables $variables
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Context $context,
        protected Registry $registryHelper,
        protected Variables $variables,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns the ID of the customer of the form, or null for a new customer.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        $customerId = $this->registryHelper->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        return $customerId === null || $customerId === '' ? null : $this->variables->intValue($customerId);
    }

    /**
     * Returns the label of the tab.
     *
     * @return string
     */
    public function getTabLabel(): string
    {
        return __('Customer Prices')->render();
    }

    /**
     * Returns the title of the tab.
     *
     * @return string
     */
    public function getTabTitle(): string
    {
        return __('Customer Prices')->render();
    }

    /**
     * The tab has no CSS class.
     *
     * @return string
     */
    public function getTabClass(): string
    {
        return '';
    }

    /**
     * Returns the URL of the grid of the customer prices of the customer.
     *
     * @return string
     */
    public function getTabUrl(): string
    {
        return $this->getUrl('product_customer_price/tab', ['_current' => true]);
    }

    /**
     * The tab loads its content with Ajax.
     *
     * @return bool
     */
    public function isAjaxLoaded(): bool
    {
        return true;
    }

    /**
     * The tab is shown for existing customers.
     *
     * @return bool
     */
    public function canShowTab(): bool
    {
        return $this->getCustomerId() !== null;
    }

    /**
     * The tab is hidden for new customers.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->getCustomerId() === null;
    }

    /**
     * Renders nothing: the content is loaded with Ajax.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return '';
    }
}
