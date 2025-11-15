<?php

namespace LFuser\CategoryContentManagement\Block\Adminhtml\Category\Product\Grid\Column;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

class ThumbnailRenderer extends AbstractRenderer
{

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected Image $imageHelper,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param Product $row
     */
    #[\Override]
    public function render(DataObject $row): string
    {
        [$src, $alt] = $this->getThumbnail($row);
        return $this->getThumbnailHtml($src, $alt);
    }

    /**
     * @return string[]
     */
    public function getThumbnail(Product $product): array
    {
        $imageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail');
        $thumbnail[] = $imageHelper->getUrl();
        $thumbnail[] = $imageHelper->getLabel() ?: $product->getName();
        return $thumbnail;
    }

    public function getThumbnailHtml(string $src, string $alt): string
    {
        return '
            <span class="thumbnail-container">
                <span class="thumbnail-wrapper">
                    <img class="admin__control-thumbnail"
                         loading="lazy"
                         src="' . $src . '"
                         alt="' . $alt . '">
                </span>
            </span>';
    }

}
