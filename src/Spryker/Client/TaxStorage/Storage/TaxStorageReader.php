<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxStorage\Storage;

use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Generated\Shared\Transfer\TaxSetStorageTransfer;
use Spryker\Client\TaxStorage\Dependency\Client\TaxStorageToStorageClientInterface;
use Spryker\Client\TaxStorage\Dependency\Service\TaxStorageToSynchronizationServiceInterface;
use Spryker\Shared\TaxStorage\TaxStorageConfig;

class TaxStorageReader implements TaxStorageReaderInterface
{
    /**
     * @var \Spryker\Client\TaxStorage\Dependency\Service\TaxStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @var \Spryker\Client\TaxStorage\Dependency\Client\TaxStorageToStorageClientInterface
     */
    protected $storageClient;

    public function __construct(
        TaxStorageToSynchronizationServiceInterface $synchronizationService,
        TaxStorageToStorageClientInterface $storageClient
    ) {
        $this->synchronizationService = $synchronizationService;
        $this->storageClient = $storageClient;
    }

    public function findTaxSetStorageByIdTaxSet(int $idTaxSet): ?TaxSetStorageTransfer
    {
        $key = $this->generateKey($idTaxSet);
        $taxSetStorageData = $this->storageClient->get($key);
        if (!$taxSetStorageData) {
            return null;
        }

        return (new TaxSetStorageTransfer())->fromArray($taxSetStorageData, true);
    }

    /**
     * @param array<int> $idTaxSets
     *
     * @return array<int, \Generated\Shared\Transfer\TaxSetStorageTransfer>
     */
    public function getTaxSetStoragesByIdTaxSets(array $idTaxSets): array
    {
        $storageKeys = array_map(fn (int $idTaxSet) => $this->generateKey($idTaxSet), $idTaxSets);
        $taxSetStorageDataItems = $this->storageClient->getMulti($storageKeys);

        $taxSetStoragesIndexedByIdTaxSet = [];

        foreach ($taxSetStorageDataItems as $taxSetStorageData) {
            if (!$taxSetStorageData) {
                continue;
            }

            $taxSetStorageData = json_decode($taxSetStorageData, true);

            if (!$taxSetStorageData) {
                continue;
            }

            $taxSetStorage = (new TaxSetStorageTransfer())->fromArray($taxSetStorageData, true);
            $taxSetStoragesIndexedByIdTaxSet[$taxSetStorage->getIdTaxSetOrFail()] = $taxSetStorage;
        }

        return $taxSetStoragesIndexedByIdTaxSet;
    }

    protected function generateKey(int $idTaxSet): string
    {
        $synchronizationDataTransfer = (new SynchronizationDataTransfer())
            ->setReference((string)$idTaxSet);

        return $this->synchronizationService
            ->getStorageKeyBuilder(TaxStorageConfig::TAX_SET_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
