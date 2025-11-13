<?php

namespace LFuser\CategoryContentManagement\Service;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryProductLinkManagement
{

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductCollectionFactory $productCollectionFactory
    ) {
    }

    /**
     * @param string[]|array{} $productSkus
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function massAssignProducts(int $categoryId, array $productSkus, string $sortingMode = 'after'): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->get($categoryId);
        $categoryProductsPosition = $category->getProductsPosition();
        $existingProductIds = array_keys($categoryProductsPosition);

        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('sku', ['in' => $productSkus]);
        if ($existingProductIds) {
            $productCollection->addFieldToFilter('entity_id', ['nin' => $existingProductIds]);
        }
        $newProductCount = $productCollection->getSize();

        $sortedCollection = [];
        /** @var Product $product */
        foreach ($productCollection as $product) {
            $pos = array_search($product->getSku(), $productSkus);
            $sortedCollection[$pos] = $product;
        }
        unset($productCollection);
        ksort($sortedCollection);

        $newProductIds = [];
        $position = 0;
        $increment = 10;

        if ($sortingMode === 'before') {
            foreach ($sortedCollection as $product) {
                $newProductIds[$product->getId()] = $position + $increment;
                $position += $increment;
            }

            $sortedProductIds = [];
            $shift = $increment * $newProductCount;
            foreach ($categoryProductsPosition as $productId => $position) {
                $newPosition = $position + $shift;
                $sortedProductIds[$productId] = $newPosition;
            }

            $merged = $newProductIds + $sortedProductIds;

        } else {
            $lastPosition = (int)end($categoryProductsPosition);
            foreach ($sortedCollection as $product) {
                $newProductIds[$product->getId()] = $lastPosition + $increment;
                $lastPosition += $increment;
            }

            $merged = $categoryProductsPosition + $newProductIds;
        }

        $category->setPostedProducts($merged);
        $this->categoryRepository->save($category);
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function truncateProducts(int $categoryId): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->get($categoryId);
        $category->setPostedProducts([]);
        $this->categoryRepository->save($category);
    }

}
