<?php
/**
 * Human Element Inc.
 *
 * @package HumanElement_DuplicateUrlKeyFix
 * @copyright Copyright (c) 2017 Human Element Inc. (https://www.human-element.com)
 */

namespace HumanElement\DuplicateUrlKeyFix\Model;

use Magento\Catalog\Model\Product;

class ProductUrlRewriteGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator
{
    /**
     * Generate list of urls for global scope
     *
     * Update adds in a clone of the collection and setting the storeId
     * Variation of this post:
     * https://magento.stackexchange.com/questions/169352/magento-2-url-key-for-specified-store-already-exists
     *
     * @param \Magento\Framework\Data\Collection $productCategories
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForGlobalScope($productCategories)
    {
        $urls = [];
        $productId = $this->product->getEntityId();
        foreach ($this->product->getStoreIds() as $id) {
            if (!$this->isGlobalScope($id)
                && !$this->storeViewService->doesEntityHaveOverriddenUrlKeyForStore($id, $productId, Product::ENTITY)
            ) {
                // Default: $urls = array_merge($urls, $this->generateForSpecificStoreView($id, $productCategories));
                // before loading the category collection by looping it, clone it and set the correct store id,
                // so we get the correct url_path & url_key for that specific store id
                $storeSpecificProductCategories = clone $productCategories;
                $storeSpecificProductCategories->setStoreId($id);

                $urls = array_merge($urls, $this->generateForSpecificStoreView($id, $storeSpecificProductCategories));
            }
        }
        return $urls;
    }
}