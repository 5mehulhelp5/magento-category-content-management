<?php

namespace LFuser\CategoryContentManagement\Plugin\Catalog\Block\Adminhtml\Category;

use LFuser\CategoryContentManagement\Block\Adminhtml\Category\Product\Grid\Column\Thumbnail;
use Magento\Catalog\Block\Adminhtml\Category\AssignProducts;
use Magento\Catalog\Block\Adminhtml\Category\Tab\Product;
use Magento\Framework\Exception\LocalizedException;

class AssignProductsPlugin
{

    /**
     * @param array<string, array<string, string>> $prependBlocks
     */
    public function __construct(
        private readonly array $prependBlocks = []
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function beforeGetGridHtml(
        AssignProducts $subject
    ): void {
        /** @var Product $gridBlock */
        $gridBlock = $subject->getBlockGrid();

        $columnId = 'thumbnail';
        /** @var Thumbnail $thumbnailBlock */
        $thumbnailBlock = $gridBlock->getLayout()->createBlock(Thumbnail::class);
        $thumbnailBlock->setData([
            'header' => __('Thumbnail'),
            'index' => $columnId,
            'type' => $columnId,
            'sortable' => false,
            'filter' => false
        ]);
        $thumbnailBlock->setId($columnId);
        $thumbnailBlock->setGrid($gridBlock);

        $gridBlock->getColumnSet()->setChild($columnId, $thumbnailBlock);
        $gridBlock->addColumnsOrder($columnId, 'entity_id');
    }

    /**
     * @throws LocalizedException
     */
    public function afterGetGridHtml(
        AssignProducts $subject,
        string $result
    ): string {
        $gridHtml = '';
        foreach ($this->prependBlocks as $block) {
            if ($subject->getLayout()->getBlock($block['name']) === false) {
                $gridHtml .= $subject->getLayout()->createBlock(
                    $block['type'],
                    $block['name'],
                )->toHtml();
            }
        }

        return $gridHtml . $result;
    }
}
