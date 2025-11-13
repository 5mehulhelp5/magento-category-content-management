<?php

namespace LFuser\CategoryContentManagement\Service;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryAttributeCopy
{

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CategoryResource $categoryResource
    ) {
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute(
        int $sourceCategoryId,
        int $sourceStoreId,
        int $targetCategoryId,
        int $targetStoreId,
        string $attributeCode
    ): void {
        /** @var Category $sourceCategory */
        $sourceCategory = $this->categoryRepository->get($sourceCategoryId, $sourceStoreId);
        if (!$sourceCategory->getId()) {
            throw new LocalizedException(__("Source category not found"));
        }

        $attributeValue = $sourceCategory->getData($attributeCode);

        /** @var Category $targetCategory */
        $targetCategory = $this->categoryRepository->get($targetCategoryId, $targetStoreId);

        if (!$targetCategory->getId()) {
            throw new LocalizedException(__("Target category not found"));
        }

        $targetCategory->setData($attributeCode, $attributeValue);
        $this->categoryResource->saveAttribute($targetCategory, $attributeCode);
    }

}
