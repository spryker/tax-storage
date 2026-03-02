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

    protected function generateKey(int $idTaxSet): string
    {
        $synchronizationDataTransfer = (new SynchronizationDataTransfer())
            ->setReference((string)$idTaxSet);

        return $this->synchronizationService
            ->getStorageKeyBuilder(TaxStorageConfig::TAX_SET_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
