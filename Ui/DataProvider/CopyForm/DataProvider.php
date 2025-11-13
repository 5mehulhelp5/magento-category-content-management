<?php

namespace LFuser\CategoryContentManagement\Ui\DataProvider\CopyForm;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param array{}|array<string, mixed> $meta
     * @param array{}|array<string, mixed> $data
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly RequestInterface $request,
        private readonly CategoryRepositoryInterface $categoryRepository,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        // @phpstan-ignore-next-line
        $this->collection = null;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array<string, array<string, int|string>>
     * @throws NoSuchEntityException
     */
    #[\Override]
    public function getData(): array
    {
        /** @var Category|null $category */
        $category = $this->getCurrentCategory();
        if (!$category) {
            return [];
        }

        return [
            $category->getId() => [
                'source_store_id' => (int)$this->storeManager->getStore()->getId(),
                'target_store_id' => '',
                'target_category_id' => '',
                'attribute_code' => '',
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function getMeta(): array
    {
        return $this->meta;
    }

    #[\Override]
    public function addFilter(Filter $filter): void
    {
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getCurrentCategory(): ?CategoryInterface
    {
        $categoryId = $this->request->getParam('id', null);
        if (!$categoryId) {
            return null;
        }

        return $this->categoryRepository->get($categoryId);
    }
}
