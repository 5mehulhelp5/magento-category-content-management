<?php

namespace LFuser\CategoryContentManagement\Block\Adminhtml\Category\Attribute;

use LFuser\CategoryContentManagement\Service\CategoryAttributeProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class CopyButton extends Template
{

    protected $_template = 'LFuser_CategoryContentManagement::category/attribute/copy-button.phtml';

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected CategoryAttributeProvider $categoryAttributeProvider,
        Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    public function getSubmitUrl(): string
    {
        $categoryId = $this->getRequest()->getParam('id', 0);
        return $this->getUrl('categorycm/category/copy', ['category_id' => $categoryId]);
    }

    public function getAttributeConfig(): string
    {
        return (string)json_encode($this->categoryAttributeProvider->getAttributes());
    }

}
