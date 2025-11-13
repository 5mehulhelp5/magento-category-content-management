<?php

namespace LFuser\CategoryContentManagement\Controller\Adminhtml\Category;

use Exception;
use LFuser\CategoryContentManagement\Service\CategoryAttributeCopy;
use LFuser\CategoryContentManagement\Service\CategoryAttributeProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Copy extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const string ADMIN_RESOURCE = 'Magento_Catalog::categories';

    public function __construct(
        Context $context,
        private readonly CategoryAttributeProvider $categoryAttributeProvider,
        private readonly CategoryAttributeCopy $categoryAttributeCopy
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     * @throws Exception
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        /** @var Http $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $result->setHttpResponseCode(405);
            return $result->setData([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $sourceId = (int)$request->getParam('category_id', 0);
        $targetId = (int)$request->getParam('target_category_id', 0);

        $attributeCode = (string)$request->getParam('attribute_code', '');
        $this->validateAttribute($attributeCode);

        $sourceStoreId = (int)$request->getParam('source_store_id', 0);
        $targetStoreId = (int)$request->getParam('target_store_id', 0);

        if (!$sourceId || !$targetId || !$attributeCode) {
            return $result->setData(['success' => false, 'message' => 'Missing parameters']);
        }

        try {
            $this->categoryAttributeCopy->execute(
                $sourceId,
                $sourceStoreId,
                $targetId,
                $targetStoreId,
                $attributeCode
            );

            return $result->setData([
                'success' => true,
                'message' => sprintf('Attribute "%s" copied', $attributeCode)
            ]);

        } catch (LocalizedException $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function validateAttribute(string $attributeCode): bool
    {
        $attributeCodes = $this->categoryAttributeProvider->execute();
        if (!in_array($attributeCode, $attributeCodes)) {
            throw new LocalizedException(__("Attribute not found"));
        }

        return true;
    }
}
