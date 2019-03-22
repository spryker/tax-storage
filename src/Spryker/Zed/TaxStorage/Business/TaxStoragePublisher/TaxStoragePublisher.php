<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxStorage\Business\TaxStoragePublisher;

use Generated\Shared\Transfer\TaxSetStorageTransfer;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\TaxStorage\Business\Mapper\TaxStorageMapperInterface;
use Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface;
use Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface;
use Spryker\Zed\TaxStorage\TaxStorageConfig;

class TaxStoragePublisher implements TaxStoragePublisherInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface
     */
    protected $taxStorageRepository;

    /**
     * @var \Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface
     */
    protected $taxStorageEntityManager;

    /**
     * @var \Spryker\Zed\TaxStorage\Business\Mapper\TaxStorageMapperInterface
     */
    protected $taxStorageMapper;
    /**
     * @var \Spryker\Zed\TaxStorage\TaxStorageConfig
     */
    protected $taxStorageConfig;

    /**
     * @param \Spryker\Zed\TaxStorage\Persistence\TaxStorageRepositoryInterface $taxStorageRepository
     * @param \Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManagerInterface $entityManager
     * @param \Spryker\Zed\TaxStorage\Business\Mapper\TaxStorageMapperInterface $taxStorageMapper
     * @param \Spryker\Zed\TaxStorage\TaxStorageConfig $taxStorageConfig
     */
    public function __construct(
        TaxStorageRepositoryInterface $taxStorageRepository,
        TaxStorageEntityManagerInterface $entityManager,
        TaxStorageMapperInterface $taxStorageMapper,
        TaxStorageConfig $taxStorageConfig
    ) {
        $this->taxStorageRepository = $taxStorageRepository;
        $this->taxStorageEntityManager = $entityManager;
        $this->taxStorageMapper = $taxStorageMapper;
        $this->taxStorageConfig = $taxStorageConfig;
    }

    /**
     * @param array $taxSetIds
     *
     * @return void
     */
    public function publishByTaxSetIds(array $taxSetIds): void
    {
        $spyTaxSets = $this->taxStorageRepository
            ->findTaxSetsByIds($taxSetIds);
        $spyTaxSetStorage = $this->taxStorageRepository
            ->findTaxSetStoragesByIds($taxSetIds)
            ->toKeyIndex('FkTaxSet');

        $this->getTransactionHandler()->handleTransaction(function () use ($spyTaxSets, $spyTaxSetStorage) {
            $this->storeDataSet($spyTaxSets, $spyTaxSetStorage);
        });
    }

    /**
     * @param array $taxSetIds
     *
     * @return void
     */
    public function unpublishByTaxSetIds(array $taxSetIds): void
    {
        $spyTaxSetStorages = $this->taxStorageRepository
            ->findTaxSetStoragesByIds($taxSetIds);

        $this->getTransactionHandler()->handleTransaction(function () use ($spyTaxSetStorages) {
            $this->executeUnpublishTransaction($spyTaxSetStorages);
        });
    }

    /**
     * @param array $taxRateIds
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
    protected function storeDataSet(iterable $spyTaxSets, iterable $spyTaxSetStorages): void
    {
        foreach ($spyTaxSets as $spyTaxSet) {
            if (isset($spyTaxSetStorages[$spyTaxSet->getIdTaxSet()])) {
                $spyTaxSetStorage = $spyTaxSetStorages[$spyTaxSet->getIdTaxSet()];
            } else {
                $spyTaxSetStorage = (new SpyTaxSetStorage())
                    ->setFkTaxSet($spyTaxSet->getIdTaxSet());
            }
            $this->createDataSet($spyTaxSet, $spyTaxSetStorage);
        }
    }

    /**
     * @param \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage[] $spyTaxSetStorages
     *
     * @return void
     */
    protected function executeUnpublishTransaction(iterable $spyTaxSetStorages): void
    {
        foreach ($spyTaxSetStorages as $spyTaxSetStorage) {
            $this->taxStorageEntityManager->deleteTaxSetStorage($spyTaxSetStorage);
        }
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $spyTaxSet
     * @param \Orm\Zed\TaxStorage\Persistence\SpyTaxSetStorage|null $spyTaxSetStorage
     *
     * @return void
     */
    protected function createDataSet(SpyTaxSet $spyTaxSet, ?SpyTaxSetStorage $spyTaxSetStorage = null): void
    {
        $taxSetStorageTransfer = new TaxSetStorageTransfer();
        $taxSetStorageTransfer->setId($spyTaxSet->getIdTaxSet());
        $taxSetStorageTransfer->fromArray($spyTaxSet->toArray(), true);
        $taxSetStorageTransfer->setTaxRates(
            $this->taxStorageMapper->mapSpyTaxRatesToTransfer($spyTaxSet->getSpyTaxRates())
        );
        $spyTaxSetStorage->setData($taxSetStorageTransfer->toArray());
        $spyTaxSetStorage->isSendingToQueue(
            $this->taxStorageConfig->isSendingToQueue()
        );

        $this->taxStorageEntityManager->saveTaxSetStorage($spyTaxSetStorage);
    }
}
