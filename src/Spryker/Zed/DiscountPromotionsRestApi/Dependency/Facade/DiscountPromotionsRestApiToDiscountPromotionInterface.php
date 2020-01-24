<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotionsRestApi\Dependency\Facade;

use Generated\Shared\Transfer\DiscountPromotionTransfer;

interface DiscountPromotionsRestApiToDiscountPromotionInterface
{
    /**
     * @param \Generated\Shared\Transfer\DiscountPromotionTransfer $discountPromotionTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountPromotionTransfer|null
     */
    public function findDiscountPromotionByUuid(DiscountPromotionTransfer $discountPromotionTransfer): ?DiscountPromotionTransfer;
}