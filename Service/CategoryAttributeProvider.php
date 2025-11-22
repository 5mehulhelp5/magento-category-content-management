<?php

namespace LFuser\CategoryContentManagement\Service;

use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute;

class CategoryAttributeProvider
{

    /**
     * @var Attribute[]|array{}|null
     */
    private ?array $categoryAttributes = null;

    public function __construct(
        private readonly CollectionFactory $attributeCollectionFactory
    ) {
    }

    /**
     * @return Attribute[]|array{}
     */
    public function getAttributes(bool $skipGlobal = false): array
    {
        if ($this->categoryAttributes === null) {
            /** @var Collection $collection */
            $collection = $this->attributeCollectionFactory->create();
            $collection->addFieldToFilter('is_visible', ['eq' => 1]);
            if ($skipGlobal) {
                $collection->addFieldToFilter('is_global', ['eq' => false]);
            }

            $result = [];
            foreach ($collection as $attribute) {
                $result[$attribute->getAttributeCode()] = $attribute;
            }

            $this->categoryAttributes = $result;
        }

        return $this->categoryAttributes;
    }
}
