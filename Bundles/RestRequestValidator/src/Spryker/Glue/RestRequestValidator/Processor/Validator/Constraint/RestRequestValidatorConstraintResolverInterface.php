<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\RestRequestValidator\Processor\Validator\Constraint;

interface RestRequestValidatorConstraintResolverInterface
{
    /**
     * @param string $className
     *
     * @return null|string
     */
    public function resolveConstraintClassName(string $className): ?string;
}
