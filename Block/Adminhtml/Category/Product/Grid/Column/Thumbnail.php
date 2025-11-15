<?php

namespace LFuser\CategoryContentManagement\Block\Adminhtml\Category\Product\Grid\Column;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Catalog\Helper\Image;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Thumbnail extends Column
{

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected Image $imageHelper,
        Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->_rendererTypes['thumbnail'] = ThumbnailRenderer::class;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

}
