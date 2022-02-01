<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\DiscountPromotionsRestApi\Processor\Expander;

use ArrayObject;
use Generated\Shared\Transfer\PromotionItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestPromotionalItemsAttributesTransfer;
use Spryker\Glue\DiscountPromotionsRestApi\DiscountPromotionsRestApiConfig;
use Spryker\Glue\DiscountPromotionsRestApi\Processor\Mapper\PromotionItemMapperInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class PromotionItemByQuoteResourceRelationshipExpander implements PromotionItemByQuoteResourceRelationshipExpanderInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \Spryker\Glue\DiscountPromotionsRestApi\Processor\Mapper\PromotionItemMapperInterface
     */
    protected $promotionItemMapper;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\DiscountPromotionsRestApi\Processor\Mapper\PromotionItemMapperInterface $promotionItemMapper
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        PromotionItemMapperInterface $promotionItemMapper
    ) {
        $this->restResourceBuilder = $restResourceBuilder;
        $this->promotionItemMapper = $promotionItemMapper;
    }

    /**
     * @param array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface> $resources
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return void
     */
    public function addResourceRelationships(array $resources, RestRequestInterface $restRequest): void
    {
        foreach ($resources as $resource) {
            $promotionItemTransfers = $this->getPromotionItemsFromPayload($resource);
            $promotionItemTransfers = $this->filterDiscountPromotionDuplicates($promotionItemTransfers);

            foreach ($promotionItemTransfers as $promotionItemTransfer) {
                $restPromotionalItemsAttributesTransfer = $this->promotionItemMapper
                    ->mapPromotionItemTransferToRestPromotionalItemsAttributesTransfer(
                        $promotionItemTransfer,
                        new RestPromotionalItemsAttributesTransfer(),
                    );

                $promotionalItemsResource = $this->restResourceBuilder->createRestResource(
                    DiscountPromotionsRestApiConfig::RESOURCE_PROMOTIONAL_ITEMS,
                    $this->findDiscountPromotionUuid($promotionItemTransfer),
                    $restPromotionalItemsAttributesTransfer,
                );

                $resource->addRelationship($promotionalItemsResource);
            }
        }
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PromotionItemTransfer> $promotionItemTransfers
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\PromotionItemTransfer>
     */
    protected function filterDiscountPromotionDuplicates(ArrayObject $promotionItemTransfers): ArrayObject
    {
        $filteredPromotionItemTransfers = [];
        foreach ($promotionItemTransfers as $promotionItemTransfer) {
            if (!isset($filteredPromotionItemTransfers[$promotionItemTransfer->getUuid()])) {
                $filteredPromotionItemTransfers[$promotionItemTransfer->getUuid()] = $promotionItemTransfer;
            }
        }

        return new ArrayObject(array_values($filteredPromotionItemTransfers));
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface $resource
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\PromotionItemTransfer>
     */
    protected function getPromotionItemsFromPayload(RestResourceInterface $resource): ArrayObject
    {
        /**
         * @var \Generated\Shared\Transfer\QuoteTransfer|null $payload
         */
        $payload = $resource->getPayload();
        if ($payload === null || !($payload instanceof QuoteTransfer)) {
            return new ArrayObject();
        }

        return $payload->getPromotionItems();
    }

    /**
     * @param \Generated\Shared\Transfer\PromotionItemTransfer $promotionItemTransfer
     *
     * @return string|null
     */
    protected function findDiscountPromotionUuid(PromotionItemTransfer $promotionItemTransfer): ?string
    {
        if ($promotionItemTransfer->getUuid() !== null) {
            return $promotionItemTransfer->getUuid();
        }

        $discountTransfer = $promotionItemTransfer->getDiscount();
        if ($discountTransfer !== null && $discountTransfer->getDiscountPromotion() !== null) {
            return $discountTransfer->getDiscountPromotionOrFail()
                ->getUuid();
        }

        return null;
    }
}
