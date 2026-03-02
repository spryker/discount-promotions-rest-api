<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotionsRestApi\Dependency\Facade;

use Generated\Shared\Transfer\DiscountPromotionCollectionTransfer;
use Generated\Shared\Transfer\DiscountPromotionCriteriaTransfer;

interface DiscountPromotionsRestApiToDiscountPromotionFacadeInterface
{
    public function getDiscountPromotionCollection(DiscountPromotionCriteriaTransfer $discountPromotionCriteriaTransfer): DiscountPromotionCollectionTransfer;
}
