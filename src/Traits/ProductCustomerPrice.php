<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Traits;

/**
 * The settings of the admin pages of the customer prices.
 */
trait ProductCustomerPrice
{
    /**
     * Returns the key of the module.
     *
     * @return string
     */
    protected function getModuleKey(): string
    {
        return 'Infrangible_CatalogProductCustomerPrice';
    }

    /**
     * Returns the key of the ACL resource.
     *
     * @return string
     */
    protected function getResourceKey(): string
    {
        return 'infrangible_catalogproductcustomerprice';
    }

    /**
     * Returns the key of the menu item.
     *
     * @return string
     */
    protected function getMenuKey(): string
    {
        return 'infrangible_catalogproductcustomerprice_manage';
    }

    /**
     * Returns the name of the object.
     *
     * @return string
     */
    protected function getObjectName(): string
    {
        return 'ProductCustomerPrice';
    }

    /**
     * Returns the field of the object ID.
     *
     * @return string|null
     */
    protected function getObjectField(): ?string
    {
        return 'id';
    }

    /**
     * Returns the title of the pages.
     *
     * @return string
     */
    protected function getTitle(): string
    {
        return __('Customer Price')->render();
    }

    /**
     * Customer prices can be added.
     *
     * @return bool
     */
    protected function allowAdd(): bool
    {
        return true;
    }

    /**
     * Customer prices can be edited.
     *
     * @return bool
     */
    protected function allowEdit(): bool
    {
        return true;
    }

    /**
     * Customer prices have no view page.
     *
     * @return bool
     */
    protected function allowView(): bool
    {
        return false;
    }

    /**
     * Customer prices can be deleted.
     *
     * @return bool
     */
    protected function allowDelete(): bool
    {
        return true;
    }

    /**
     * Several customer prices can be deleted at once.
     *
     * @return bool
     */
    protected function allowMassDelete(): bool
    {
        return true;
    }

    /**
     * Returns the message if the customer price was not found.
     *
     * @return string
     */
    protected function getObjectNotFoundMessage(): string
    {
        return __('Unable to find the customer price with id: %s')->render();
    }
}
