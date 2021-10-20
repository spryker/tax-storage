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
     * @var string
     */
    protected const COL_FK_TAX_SET = 'FkTaxSet';

    /**
     * @param array<\Generated\Shared\Transfer\TaxSetStorageTransfer> $taxSetStorageTransfers
     *
     * @return void
     */
    public function saveTaxSetStorage(array $taxSetStorageTransfers): void
    {
        $taxStorageConfig = $this->getFactory()->getConfig();

        $taxSetStorageTransfersToUpdate = $this->findTaxSetStoragesByIdTaxSetsIndexedByFkTaxSet(
            $this->getIdFromTransfers($taxSetStorageTransfers),
        );

        foreach ($taxSetStorageTransfers as $taxSetStorageTransfer) {
            $spyTaxSetStorage = $taxSetStorageTransfersToUpdate[$taxSetStorageTransfer->getIdTaxSet()] ?? (new SpyTaxSetStorage())
                    ->setFkTaxSet($taxSetStorageTransfer->getIdTaxSet());
            $spyTaxSetStorage->setData($taxSetStorageTransfer->toArray());
            $spyTaxSetStorage->setIsSendingToQueue($taxStorageConfig->isSendingToQueue());

            $spyTaxSetStorage->save();
        }
    }

    /**
     * @param array<int> $taxSetIds
     *
     * @return void
     */
    public function deleteTaxSetStoragesByIds(array $taxSetIds): void
    {
        $spyTaxSetStorages = $this->findTaxSetStoragesByIdTaxSetsIndexedByFkTaxSet($taxSetIds);

        foreach ($spyTaxSetStorages as $spyTaxSetStorage) {
            $spyTaxSetStorage->delete();
        }
    }

    /**
     * @param array<int> $taxSetIds
     *
     * @return array<\Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage>
     */
    public function findTaxSetStoragesByIdTaxSetsIndexedByFkTaxSet(array $taxSetIds): array
    {
        $spyTaxSetStorage = $this->getFactory()
            ->createTaxSetStorageQuery()
            ->filterByFkTaxSet_In($taxSetIds)
            ->find()
            ->toKeyIndex(static::COL_FK_TAX_SET);

        return $spyTaxSetStorage;
    }

    /**
     * @param array<\Generated\Shared\Transfer\TaxSetStorageTransfer> $taxSetStorageTransfers
     *
     * @return array<int>
     */
    protected function getIdFromTransfers(array $taxSetStorageTransfers): array
    {
        $ids = [];
        foreach ($taxSetStorageTransfers as $taxSetStorageTransfer) {
            $ids[] = $taxSetStorageTransfer->getIdTaxSet();
        }

        return $ids;
    }
}
