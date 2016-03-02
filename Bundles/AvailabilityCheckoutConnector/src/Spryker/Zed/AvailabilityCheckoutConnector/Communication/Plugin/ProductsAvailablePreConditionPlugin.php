<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\AvailabilityCheckoutConnector\Communication\Plugin;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutRequestTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Spryker\Shared\Checkout\CheckoutConstants;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\AvailabilityCheckoutConnector\Communication\AvailabilityCheckoutConnectorCommunicationFactory getFactory()
 */
class ProductsAvailablePreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
{

    /**
     * @param string $sku
     * @param int $quantity
     *
     * @return bool
     */
    protected function isProductSellable($sku, $quantity)
    {
        return $this->getFactory()->getAvailabilityFacade()->isProductSellable($sku, $quantity);
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutRequestTransfer $checkoutRequest
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function checkCondition(CheckoutRequestTransfer $checkoutRequest, CheckoutResponseTransfer $checkoutResponse)
    {
        $groupedItemQuantities = $this->groupItemsBySku($checkoutRequest->getCart()->getItems());

        foreach ($groupedItemQuantities as $sku => $quantity) {
            if (!$this->isProductSellable($sku, $quantity)) {
                $error = new CheckoutErrorTransfer();
                $error
                    ->setErrorCode(CheckoutConstants::ERROR_CODE_PRODUCT_UNAVAILABLE)
                    ->setMessage('product.unavailable');

                $checkoutResponse
                    ->addError($error)
                    ->setIsSuccess(false);
            }
        }
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return array
     */
    private function groupItemsBySku(\ArrayObject $items)
    {
        $result = [];

        foreach ($items as $item) {
            $sku = $item->getSku();

            if (!isset($result[$sku])) {
                $result[$sku] = 0;
            }
            $result[$sku] += $item->getQuantity();
        }

        return $result;
    }

}
