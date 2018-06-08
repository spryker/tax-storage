<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPackagingUnit\Business;

use Generated\Shared\Transfer\ProductPackagingLeadProductTransfer;
use Generated\Shared\Transfer\ProductPackagingUnitTypeTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\ProductPackagingUnit\Business\ProductPackagingUnitBusinessFactory getFactory()
 */
class ProductPackagingUnitFacade extends AbstractFacade implements ProductPackagingUnitFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return void
     */
    public function installProductPackagingUnitTypes(): void
    {
        $this->getFactory()
            ->createProductPackagingUnitTypeInstaller()
            ->install();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductPackagingUnitTypeTransfer $productPackagingUnitTypeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductPackagingUnitTypeTransfer
     */
    public function getProductPackagingUnitTypeByName(
        ProductPackagingUnitTypeTransfer $productPackagingUnitTypeTransfer
    ): ProductPackagingUnitTypeTransfer {
        return $this->getFactory()
            ->createProductPackagingUnitTypeReader()
            ->getProductPackagingUnitTypeByName($productPackagingUnitTypeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $productAbstractId
     *
     * @return \Generated\Shared\Transfer\ProductPackagingLeadProductTransfer|null
     */
    public function getProductPackagingLeadProductByAbstractId(
        int $productAbstractId
    ): ?ProductPackagingLeadProductTransfer {
        return $this->getFactory()
            ->createProductPackagingUnitTypeReader()
            ->getProductPackagingLeadProductByAbstractId($productAbstractId);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getDefaultPackagingUnitTypeName(): string
    {
        return $this->getFactory()->getConfig()->getDefaultPackagingUnitTypeName();
    }
}
