<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MinimumOrderValue;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use Spryker\Zed\MinimumOrderValue\Business\Strategy\HardThresholdStrategy;
use Spryker\Zed\MinimumOrderValue\Business\Strategy\SoftThresholdWithFixedFeeStrategy;
use Spryker\Zed\MinimumOrderValue\Business\Strategy\SoftThresholdWithFlexibleFeeStrategy;
use Spryker\Zed\MinimumOrderValue\Business\Strategy\SoftThresholdWithMessageStrategy;

class MinimumOrderValueConfig extends AbstractBundleConfig
{
    /**
     * @uses CalculationPriceMode::PRICE_MODE_NET
     */
    protected const PRICE_MODE_NET = 'NET_MODE';

    /**
     * @return \Spryker\Zed\MinimumOrderValue\Business\Strategy\MinimumOrderValueStrategyInterface[]
     */
    public function getMinimumOrderValueStrategies(): array
    {
        return [
            new HardThresholdStrategy(),
            new SoftThresholdWithMessageStrategy(),
            new SoftThresholdWithFixedFeeStrategy(),
            new SoftThresholdWithFlexibleFeeStrategy(),
        ];
    }

    /**
     * @return string
     */
    public function getNetPriceMode(): string
    {
        return static::PRICE_MODE_NET;
    }
}
