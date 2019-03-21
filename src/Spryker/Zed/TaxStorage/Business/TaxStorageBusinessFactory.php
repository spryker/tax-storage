<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\TaxStorage\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\TaxStorage\Business\Mapper\TaxStorageMapper;
use Spryker\Zed\TaxStorage\Business\Mapper\TaxStorageMapperInterface;
use Spryker\Zed\TaxStorage\Business\TaxStoragePublisher\TaxStoragePublisher;
use Spryker\Zed\TaxStorage\Business\TaxStoragePublisher\TaxStoragePublisherInterface;

/**
 * @method \Spryker\Zed\TaxStorage\Persistence\TaxStorageRepository getRepository()
 * @method \Spryker\Zed\TaxStorage\Persistence\TaxStorageEntityManager getEntityManager()
 */
class TaxStorageBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\TaxStorage\Business\TaxStoragePublisher\TaxStoragePublisherInterface
     */
    public function createTaxStoragePublisher(): TaxStoragePublisherInterface
    {
        return new TaxStoragePublisher(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createTaxStorageMapper()
        );
    }

    /**
     * @return \Spryker\Zed\TaxStorage\Persistence\Mapper\TaxStorageMapperInterface
     */
    public function createTaxStorageMapper(): TaxStorageMapperInterface
    {
        return new TaxStorageMapper();
    }
}
