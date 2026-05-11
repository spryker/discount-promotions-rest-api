<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\DiscountPromotionsRestApi\Api\Storefront\Relationship;

use Generated\Api\Storefront\PromotionalItemsStorefrontResource;
use Generated\Shared\Transfer\DiscountPromotionTransfer;
use Generated\Shared\Transfer\PromotionItemTransfer;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;

/**
 * Builds `PromotionalItems` sub-resources from `promotionItems` carried on a `Carts`/`GuestCarts`
 * parent resource. Mirrors the legacy {@see \Spryker\Glue\DiscountPromotionsRestApi\Processor\Expander\PromotionItemByQuoteResourceRelationshipExpander}
 * behavior: data is read directly from the parent's QuoteTransfer-derived promotion items —
 * no extra cart fetch — and duplicates are filtered by discount-promotion uuid.
 */
class CartsPromotionalItemsRelationshipResolver extends AbstractRelationshipResolver
{
    /**
     * @return array<\Generated\Api\Storefront\PromotionalItemsStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $resources = [];
        $seenUuids = [];

        foreach ($this->getParentResources() as $parent) {
            $promotionItems = $parent->promotionItems ?? [];

            foreach ($promotionItems as $promotionItemTransfer) {
                if (!$promotionItemTransfer instanceof PromotionItemTransfer) {
                    continue;
                }

                $uuid = $this->findDiscountPromotionUuid($promotionItemTransfer);

                if ($uuid === null || isset($seenUuids[$uuid])) {
                    continue;
                }

                $seenUuids[$uuid] = true;
                $resources[] = $this->mapPromotionItemToResource($promotionItemTransfer, $uuid);
            }
        }

        return $resources;
    }

    protected function mapPromotionItemToResource(
        PromotionItemTransfer $promotionItemTransfer,
        string $uuid,
    ): PromotionalItemsStorefrontResource {
        $discountPromotionTransfer = $promotionItemTransfer->getDiscount() !== null
            ? $promotionItemTransfer->getDiscount()->getDiscountPromotion()
            : null;

        $resource = new PromotionalItemsStorefrontResource();
        $resource->uuid = $uuid;
        $resource->sku = $discountPromotionTransfer !== null
            ? $this->getAbstractSku($discountPromotionTransfer)
            : null;
        $resource->quantity = $promotionItemTransfer->getMaxQuantity();

        return $resource;
    }

    protected function findDiscountPromotionUuid(PromotionItemTransfer $promotionItemTransfer): ?string
    {
        if ($promotionItemTransfer->getUuid() !== null) {
            return $promotionItemTransfer->getUuid();
        }

        $discountTransfer = $promotionItemTransfer->getDiscount();

        if ($discountTransfer !== null && $discountTransfer->getDiscountPromotion() !== null) {
            return $discountTransfer->getDiscountPromotionOrFail()->getUuid();
        }

        return null;
    }

    protected function getAbstractSku(DiscountPromotionTransfer $discountPromotionTransfer): ?string
    {
        $abstractSku = $discountPromotionTransfer->getAbstractSku();

        if ($abstractSku !== null && $abstractSku !== '') {
            return $abstractSku;
        }

        $abstractSkus = $discountPromotionTransfer->getAbstractSkus();

        if ($abstractSkus !== [] && isset($abstractSkus[0])) {
            return $abstractSkus[0];
        }

        return null;
    }
}
