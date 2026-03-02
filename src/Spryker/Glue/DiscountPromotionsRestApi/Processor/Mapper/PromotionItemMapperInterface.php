<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\DiscountPromotionsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\PromotionItemTransfer;
use Generated\Shared\Transfer\RestDiscountsAttributesTransfer;
use Generated\Shared\Transfer\RestPromotionalItemsAttributesTransfer;

interface PromotionItemMapperInterface
{
    public function mapPromotionItemTransferToRestPromotionalItemsAttributesTransfer(
        PromotionItemTransfer $promotionItemTransfer,
        RestPromotionalItemsAttributesTransfer $restPromotionalItemsAttributesTransfer
    ): RestPromotionalItemsAttributesTransfer;

    public function mapDiscountPromotionToRestDiscountsAttributesTransfer(
        DiscountTransfer $discountTransfer,
        RestDiscountsAttributesTransfer $restDiscountsAttributesTransfer
    ): RestDiscountsAttributesTransfer;
}
