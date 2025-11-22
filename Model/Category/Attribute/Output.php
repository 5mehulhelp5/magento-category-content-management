<?php

namespace LFuser\CategoryContentManagement\Model\Category\Attribute;

use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

readonly class Output
{

    public function __construct(
        private StoreManagerInterface $storeManager,
        private OutputHelper $outputHelper
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function getValue(Attribute $attribute, Category $category): string
    {
        $attributeCode = $attribute->getAttributeCode();
        if ($attribute->getSourceModel()) {
            return (string)$attribute->getSource()->getOptionText($category->getData("{$attributeCode}_stores"));
        }

        if ($attribute->getFrontendInput() === 'image') {
            $imageUrl = $this->storeManager->getStore()->getBaseUrl()
                . ltrim((string) $category->getData("{$attributeCode}_stores"), '/');
            return sprintf('<img src="%s"/>', $imageUrl);
        }

        return $this->outputHelper->categoryAttribute(
            $category,
            $category->getData("{$attributeCode}_stores"),
            $attributeCode
        );
    }
}
