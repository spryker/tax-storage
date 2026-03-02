<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\TaxStorage\Dependency\Client\TaxStorageToStorageClientInterface;
use Spryker\Client\TaxStorage\Dependency\Service\TaxStorageToSynchronizationServiceInterface;
use Spryker\Client\TaxStorage\Storage\TaxStorageReader;
use Spryker\Client\TaxStorage\Storage\TaxStorageReaderInterface;

class TaxStorageFactory extends AbstractFactory
{
    public function createTaxStorageReader(): TaxStorageReaderInterface
    {
        return new TaxStorageReader(
            $this->getSynchronizationService(),
            $this->getStorageClient(),
        );
    }

    public function getSynchronizationService(): TaxStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(TaxStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    public function getStorageClient(): TaxStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(TaxStorageDependencyProvider::CLIENT_STORAGE);
    }
}
