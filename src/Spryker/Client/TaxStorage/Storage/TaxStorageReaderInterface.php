<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxStorage\Storage;

use Generated\Shared\Transfer\TaxSetStorageTransfer;

interface TaxStorageReaderInterface
{
    public function findTaxSetStorageByIdTaxSet(int $idTaxSet): ?TaxSetStorageTransfer;

    /**
     * @param array<int> $idTaxSets
     *
     * @return array<int, \Generated\Shared\Transfer\TaxSetStorageTransfer>
     */
    public function getTaxSetStoragesByIdTaxSets(array $idTaxSets): array;
}
