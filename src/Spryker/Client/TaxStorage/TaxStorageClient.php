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
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idTaxSet
     *
     * @return \Generated\Shared\Transfer\TaxSetStorageTransfer|null
     */
    public function findTaxSetStorage(int $idTaxSet): ?TaxSetStorageTransfer
    {
        return $this->getFactory()
            ->createTaxStorageReader()
            ->findTaxSetStorage($idTaxSet);
    }
}
