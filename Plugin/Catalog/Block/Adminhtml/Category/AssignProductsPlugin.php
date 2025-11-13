<?php

namespace LFuser\CategoryContentManagement\Plugin\Catalog\Block\Adminhtml\Category;

use Magento\Catalog\Block\Adminhtml\Category\AssignProducts;
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
