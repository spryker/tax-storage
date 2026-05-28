<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxStorage;

use Generated\Shared\Transfer\TaxSetStorageTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Spryker\Client\TaxStorage\TaxStorageFactory getFactory()
 */
class TaxStorageClient extends AbstractClient implements TaxStorageClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idTaxSet
     *
     * @return \Generated\Shared\Transfer\TaxSetStorageTransfer|null
     */
    public function findTaxSetStorageByIdTaxSet(int $idTaxSet): ?TaxSetStorageTransfer
    {
        return $this->getFactory()
            ->createTaxStorageReader()
            ->findTaxSetStorageByIdTaxSet($idTaxSet);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $idTaxSets
     *
     * @return array<int, \Generated\Shared\Transfer\TaxSetStorageTransfer>
     */
    public function getTaxSetStoragesByIdTaxSets(array $idTaxSets): array
    {
        return $this->getFactory()
            ->createTaxStorageReader()
            ->getTaxSetStoragesByIdTaxSets($idTaxSets);
    }
}
