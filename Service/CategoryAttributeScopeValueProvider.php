<?php

namespace LFuser\CategoryContentManagement\Service;

use LFuser\CategoryContentManagement\Model\Category\Attribute\Output;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class CategoryAttributeScopeValueProvider
{

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly CategoryAttributeProvider $categoryAttributeProvider,
        private readonly CollectionFactory $categoryCollectionFactory,
        private readonly Output $output
    ) {
    }

    /**
     * @throws LocalizedException
     * @return array<string, array<string, mixed>>
     */
    public function execute(int $categoryId): array
    {
        $scopedVariations = [];
        $attributes = $this->categoryAttributeProvider->getAttributes(true);
        $attributeCodes = array_keys($attributes);

        $stores = $this->storeManager->getStores();
        /** @var Store $store */
        foreach ($stores as $store) {
            $category = $this->getScopedCategory($categoryId, $store, $attributes, $attributeCodes);
            $this->getScopedAttributeValues($category, $attributes, $store->getCode(), $scopedVariations);
        }

        return $scopedVariations;
    }

    /**
     * @param Attribute[] $attributes
     * @param string[] $attributeCodes
     * @throws LocalizedException
     */
    private function getScopedCategory(
        int $categoryId,
        Store $store,
        array $attributes,
        array $attributeCodes
    ): Category {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect($attributeCodes);
        $storeId = $store->getId();

        foreach ($attributes as $attribute) {
            $backendTable = $attribute->getBackendTable();
            $alias = "{$attribute->getAttributeCode()}_stores";
            $collection->getSelect()->joinLeft(
                [$alias => $backendTable],
                "e.entity_id = $alias.entity_id 
                 AND $alias.attribute_id = {$attribute->getAttributeId()}
                 AND $alias.store_id = {$storeId}",
                [$alias => 'value']
            );
        }

        $collection->addFieldToFilter('entity_id', ['eq' => $categoryId]);
        $collection->setStore($store);
        $collection->setPageSize(1);

        /** @var Category $category */
        $category = $collection->getFirstItem();
        return $category;
    }

    /**
     * @param Attribute[] $attributes
     * @param array<string, string[]> $scopedVariations
     * @throws LocalizedException
     */
    private function getScopedAttributeValues(
        Category $category,
        array $attributes,
        string $storeCode,
        array &$scopedVariations = []
    ): void {
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($category->getData("{$attributeCode}_stores") !== null) {
                $scopedVariations[$attributeCode][$storeCode] = $this->output->getValue($attribute, $category);
            }
        }
    }

}
