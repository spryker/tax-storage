<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxStorage\Persistence;

use Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\TaxStorage\Persistence\TaxStoragePersistenceFactory getFactory()
 */
class TaxStorageEntityManager extends AbstractEntityManager implements TaxStorageEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TaxSetStorageTransfer[] $taxSetStorageTransfers
     *
     * @return void
     */
    public function saveTaxSetStorage(array $taxSetStorageTransfers): void
    {
        $taxStorageRepository = $this->getFactory()->getRepository();
        $taxStorageConfig = $this->getFactory()->getConfig();

        $taxSetStorageTransfersToUpdate = $taxStorageRepository->findTaxSetStoragesByIdTaxSetsIndexedByFkTaxSet(
            $this->getIdFromTransfers($taxSetStorageTransfers)
        );

        foreach ($taxSetStorageTransfers as $taxSetStorageTransfer) {
            $spyTaxSetStorage = $taxSetStorageTransfersToUpdate[$taxSetStorageTransfer->getIdTaxSet()] ?? (new SpyTaxSetStorage())
                    ->setFkTaxSet($taxSetStorageTransfer->getIdTaxSet());
            $spyTaxSetStorage->setData($taxSetStorageTransfer);
            $spyTaxSetStorage->setIsSendingToQueue($taxStorageConfig->isSendingToQueue());

            $spyTaxSetStorage->save();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\TaxSetStorageTransfer[] $taxSetStorageTransfers
     *
     * @return int[]
     */
    protected function getIdFromTransfers(array $taxSetStorageTransfers): array
    {
        $ids = [];
        foreach ($taxSetStorageTransfers as $taxSetStorageTransfer) {
            $ids[] = $taxSetStorageTransfer->getIdTaxSet();
        }

        return $ids;
    }

    /**
     * @param int[] $taxSetIds
     *
     * @return void
     */
    public function deleteTaxSetStoragesByIds(array $taxSetIds): void
    {
        $taxStorageRepository = $this->getFactory()->getRepository();
        $spyTaxSetStorages = $taxStorageRepository->findTaxSetStoragesByIdTaxSetsIndexedByFkTaxSet($taxSetIds);

        foreach ($spyTaxSetStorages as $spyTaxSetStorage) {
            $spyTaxSetStorage->delete();
        }
    }
}
