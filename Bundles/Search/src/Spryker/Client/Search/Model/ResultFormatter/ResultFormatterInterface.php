<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Search\Model\ResultFormatter;

interface ResultFormatterInterface
{

    /**
     * @param mixed $searchResult
     *
     * @return mixed
     */
    public function formatResult($searchResult);

}
