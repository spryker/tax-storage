<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxStorage\Business\TaxStoragePublisher;

use ArrayObject;
use Generated\Shared\Transfer\TaxRateStorageTransfer;
use Generated\Shared\Transfer\TaxSetStorageTransfer;
use Orm\Zed\Tax\Persistence\Base\SpyTaxSet;
use Orm\Zed\Tax\Persistence\SpyTaxRate;
use Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage;
use Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface;
use Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface;
use Spryker\Zed\TaxStorage\TaxStorageConfig;

class TaxStoragePublisher implements TaxStoragePublisherInterface
{
    /**
     * @var \Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface
     */
    protected $taxStorageRepository;

    /**
     * @var \Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface
     */
    protected $taxStorageEntityManager;

    /**
     * @var \Spryker\Zed\TaxStorage\TaxStorageConfig
     */
    protected $taxStorageConfig;

    /**
     * @param \Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface $taxStorageRepository
     * @param \Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface $taxStorageEntityManager
     * @param \Spryker\Zed\TaxStorage\TaxStorageConfig $taxStorageConfig
     */
    public function __construct(
        TaxStorageRepositoryInterface $taxStorageRepository,
        TaxStorageEntityManagerInterface $taxStorageEntityManager,
        TaxStorageConfig $taxStorageConfig
    ) {
        $this->taxStorageRepository = $taxStorageRepository;
        $this->taxStorageEntityManager = $taxStorageEntityManager;
        $this->taxStorageConfig = $taxStorageConfig;
    }

    /**
     * @param int[] $taxSetIds
     *
     * @return void
     */
    public function publishByTaxSetIds(array $taxSetIds): void
    {
        $spyTaxSets = $this->taxStorageRepository
            ->findTaxSetsByIds($taxSetIds);
        $spyTaxSetStorage = $this->taxStorageRepository
            ->findTaxSetStoragesByIds($taxSetIds);

        $this->storeDataSet($spyTaxSets, $spyTaxSetStorage);
    }

    /**
     * @param int[] $taxRateIds
     *
     * @return void
     */
    public function publishByTaxRateIds(array $taxRateIds): void
    {
        $taxSetIds = $this->taxStorageRepository
            ->findTaxSetIdsByTaxRateIds($taxRateIds);

        $this->publishByTaxSetIds($taxSetIds);
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet[] $spyTaxSets
     * @param \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage[] $spyTaxSetStorages
     *
     * @return void
     */
    protected function storeDataSet(array $spyTaxSets, array $spyTaxSetStorages): void
    {
        foreach ($spyTaxSets as $spyTaxSet) {
            $spyTaxSetStorage = $spyTaxSetStorages[$spyTaxSet->getIdTaxSet()] ?? (new SpyTaxSetStorage())
                    ->setFkTaxSet($spyTaxSet->getIdTaxSet());
            $this->createDataSet($spyTaxSet, $spyTaxSetStorage);
        }
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $spyTaxSet
     * @param \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage $spyTaxSetStorage
     *
     * @return void
     */
    protected function createDataSet(SpyTaxSet $spyTaxSet, SpyTaxSetStorage $spyTaxSetStorage): void
    {
        $spyTaxSetStorage = $this->mapSpyTaxSetToTaxSetStorage($spyTaxSet, $spyTaxSetStorage);
        $spyTaxSetStorage->setIsSendingToQueue(
            $this->taxStorageConfig->isSendingToQueue()
        );

        $this->taxStorageEntityManager->saveTaxSetStorage($spyTaxSetStorage);
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\Base\SpyTaxSet $spyTaxSet
     * @param \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage $spyTaxSetStorage
     *
     * @return \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage
     */
    protected function mapSpyTaxSetToTaxSetStorage(SpyTaxSet $spyTaxSet, SpyTaxSetStorage $spyTaxSetStorage): SpyTaxSetStorage
    {
        $spyTaxSetStorage->setData($this->createTaxSetStorageTransfer($spyTaxSet)->toArray());

        return $spyTaxSetStorage;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\Base\SpyTaxSet $spyTaxSet
     *
     * @return \Generated\Shared\Transfer\TaxSetStorageTransfer
     */
    protected function createTaxSetStorageTransfer(SpyTaxSet $spyTaxSet): TaxSetStorageTransfer
    {
        $taxSetStorageTransfer = new TaxSetStorageTransfer();
        $taxSetStorageTransfer->fromArray($spyTaxSet->toArray(), true);
        $taxSetStorageTransfer->setTaxRates(
            $this->mapSpyTaxRatesToTaxRateTransfers($spyTaxSet->getSpyTaxRates()->getArrayCopy())
        );

        return $taxSetStorageTransfer;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxRate[] $spyTaxRates
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\TaxRateStorageTransfer[]
     */
    protected function mapSpyTaxRatesToTaxRateTransfers(array $spyTaxRates): ArrayObject
    {
        $taxRateTransfers = new ArrayObject();

        foreach ($spyTaxRates as $spyTaxRate) {
            $taxRateTransfers->append(
                $this->mapSpyTaxRateToTaxRateStorageTransfer($spyTaxRate, new TaxRateStorageTransfer())
            );
        }

        return $taxRateTransfers;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxRate $spyTaxRate
     * @param \Generated\Shared\Transfer\TaxRateStorageTransfer $taxRateStorageTransfer
     *
     * @return \Generated\Shared\Transfer\TaxRateStorageTransfer
     */
    protected function mapSpyTaxRateToTaxRateStorageTransfer(
        SpyTaxRate $spyTaxRate,
        TaxRateStorageTransfer $taxRateStorageTransfer
    ): TaxRateStorageTransfer {
        $taxRateStorageTransfer->fromArray($spyTaxRate->toArray(), true);
        if ($spyTaxRate->getCountry() !== null) {
            $taxRateStorageTransfer->setCountry($spyTaxRate->getCountry()->getName());
        }

        return $taxRateStorageTransfer;
    }
}
