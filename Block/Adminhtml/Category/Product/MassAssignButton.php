<?php

namespace LFuser\CategoryContentManagement\Block\Adminhtml\Category\Product;

use Magento\Backend\Block\Template;

class MassAssignButton extends Template
{

    protected $_template = 'LFuser_CategoryContentManagement::category/product/mass-assign-button.phtml';

    public function getSubmitUrl(): string
    {
        $categoryId = $this->getRequest()->getParam('id', 0);
        return $this->getUrl('categorycm/category/massAssign', ['category_id' => $categoryId]);
    }
}
