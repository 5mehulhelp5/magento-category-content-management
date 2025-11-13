<?php

namespace LFuser\CategoryContentManagement\Service;

use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;
use Magento\Eav\Model\Attribute;

class CategoryAttributeProvider
{

    public function __construct(
        private readonly CollectionFactory $attributeCollectionFactory
    ) {
    }

    /**
     * @return string[]|array{}
     */
    public function execute(): array
    {
        /** @var Collection $collection */
        $collection = $this->attributeCollectionFactory->create();
        $attributeCodes = [];

        /** @var Attribute $attribute */
        foreach ($collection as $attribute) {
            if (!$attribute->getAttributeCode()) {
                continue;
            }

            $attributeCodes[] = (string)$attribute->getAttributeCode();
        }

        return $attributeCodes;
    }
}
