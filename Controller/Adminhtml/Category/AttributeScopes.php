<?php

namespace LFuser\CategoryContentManagement\Controller\Adminhtml\Category;

use LFuser\CategoryContentManagement\Service\CategoryAttributeScopeValueProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class AttributeScopes extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const string ADMIN_RESOURCE = 'Magento_Catalog::categories';

    public function __construct(
        Context $context,
        private readonly CategoryAttributeScopeValueProvider $categoryAttributeScopeValueProvider,
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        /** @var Http $request */
        $request = $this->getRequest();

        $categoryId = (int)$request->getParam('category_id', 0);
        if (!$categoryId) {
            return $result->setData(['success' => false, 'message' => 'Missing parameters']);
        }

        try {
            $attributes = $this->categoryAttributeScopeValueProvider->execute($categoryId);

            return $result->setData([
                'success' => true,
                'attributes' => $attributes
            ]);

        } catch (LocalizedException $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
