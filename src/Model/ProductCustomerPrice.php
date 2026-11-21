<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductCustomerPrice\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * A price of a product for a customer: a fixed price or a discount in percent, optionally limited to a quantity.
 *
 * @method string|null getCustomerId()
 * @method void setCustomerId(int|string $customerId)
 * @method string|null getProductId()
 * @method void setProductId(int|string $productId)
 * @method string|null getPrice()
 * @method void setPrice(float|string|null $price)
 * @method string|null getDiscount()
 * @method void setDiscount(int|string|null $discount)
 * @method string|null getLimit()
 * @method void setLimit(int|string|null $limit)
 * @method string|null getPriority()
 * @method void setPriority(int|string $priority)
 * @method string|null getWebsiteId()
 * @method void setWebsiteId(int|string $websiteId)
 * @method string|null getUsed()
 * @method void setUsed(int|string $used)
 * @method string|null getActive()
 * @method void setActive(int|string $active)
 */
class ProductCustomerPrice extends AbstractModel
{
    /** @var string */
    protected $_eventPrefix = 'product_customer_price';

    /**
     * Initializes the resource model.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\ProductCustomerPrice::class);
    }
}
