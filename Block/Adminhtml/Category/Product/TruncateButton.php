<?php

namespace LFuser\CategoryContentManagement\Block\Adminhtml\Category\Product;

use Magento\Backend\Block\Template;

class TruncateButton extends Template
{

    protected $_template = 'LFuser_CategoryContentManagement::category/product/truncate-button.phtml';

    public function getSubmitUrl(): string
    {
        $categoryId = $this->getRequest()->getParam('id', 0);
        return $this->getUrl('categorycm/category/truncate', ['category_id' => $categoryId]);
    }
}
