<?php

namespace LFuser\CategoryContentManagement\Plugin\Catalog\Block\Adminhtml\Category\Tab;

use Magento\Catalog\Block\Adminhtml\Category\Tab\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;

class ProductPlugin
{

    /**
     * @param ProductCollection $collection
     * @return Collection[]
     * @throws LocalizedException
     */
    public function beforeSetCollection(
        Product $subject,
        $collection
    ): array {
        $collection->addAttributeToSelect(['thumbnail']);
        return [$collection];
    }

}
