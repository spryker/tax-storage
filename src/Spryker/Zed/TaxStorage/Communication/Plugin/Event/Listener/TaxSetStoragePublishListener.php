<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxStorage\Communication\Plugin\Event\Listener;

use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

/**
 * @method \Spryker\Zed\TaxStorage\TaxStorageConfig getConfig()
 * @method \Spryker\Zed\TaxStorage\Business\TaxStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\TaxStorage\Communication\TaxStorageCommunicationFactory getFactory()
 */
class TaxSetStoragePublishListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        $this->preventTransaction();
        $taxSetIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferIds($eventEntityTransfers);
        $this->getFacade()->publishByTaxSetIds($taxSetIds);
    }
}
