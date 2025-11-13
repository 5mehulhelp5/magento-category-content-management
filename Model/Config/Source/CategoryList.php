<?php

declare(strict_types=1);

namespace LFuser\CategoryContentManagement\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class CategoryList implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $categoryCollectionFactory
    ) {
    }

    /**
     * @return array<int, array<string, string>>
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name')
            ->addAttributeToFilter('level', ['gt' => 0])
            ->setOrder('path', 'ASC');

        $options = [];

        foreach ($collection as $category) {
            $value = (int)($category->getLevel()) === 1 ? 0 : $category->getId();
            $options[] = [
                'value' => $value,
                'label' => str_repeat('=> ', max(0, (int)$category->getLevel() - 1)) . $category->getName(),
            ];
        }

        return $options;
    }
}
