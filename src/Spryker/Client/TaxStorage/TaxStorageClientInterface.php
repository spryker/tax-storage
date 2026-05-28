<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\TaxStorage;

use Generated\Shared\Transfer\TaxSetStorageTransfer;

interface TaxStorageClientInterface
{
    /**
     * Specification:
     * - Finds tax sets with related tax rates data in storage.
     *
     * @api
     *
     * @param int $idTaxSet
     *
     * @return \Generated\Shared\Transfer\TaxSetStorageTransfer|null
     */
    public function findTaxSetStorageByIdTaxSet(int $idTaxSet): ?TaxSetStorageTransfer;

    /**
     * Specification:
     *  - Finds tax sets within Storage for a given list of tax set IDs.
     *  - Returns array indexed by tax set ID.
     *  - Skips IDs not found in storage.
     *
     * @api
     *
     * @param array<int> $idTaxSets
     *
     * @return array<int, \Generated\Shared\Transfer\TaxSetStorageTransfer>
     */
    public function getTaxSetStoragesByIdTaxSets(array $idTaxSets): array;
}
