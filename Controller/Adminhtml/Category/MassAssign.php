<?php

namespace LFuser\CategoryContentManagement\Controller\Adminhtml\Category;

use LFuser\CategoryContentManagement\Service\CategoryProductLinkManagement;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class MassAssign extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const string ADMIN_RESOURCE = 'Magento_Catalog::categories';

    public function __construct(
        Context $context,
        private readonly CategoryProductLinkManagement $categoryProductLinkManagement
    ) {
        parent::__construct($context);
    }

    /**
     * @return Json
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
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

        $categoryId = $this->getRequest()->getParam('category_id');
        if (!$categoryId) {
            $result->setHttpResponseCode(405);
            return $result->setData([
                'message' => __('Cannot assign product to category, missing category id.')->render(),
                'success' => false
            ]);
        }

        $productSkus = $this->getRequest()->getParam('assign_products');
        if (!$productSkus) {
            $result->setHttpResponseCode(405);
            return $result->setData([
                'message' => __('Cannot assign product to category, empty skus list.')->render(),
                'success' => false
            ]);
        }

        $sortingMode = $this->getRequest()->getParam('sorting_mode', 'after');

        $productSkus = preg_split('/\n|\r\n?/', (string) $productSkus) ?: [];
        $this->categoryProductLinkManagement->massAssignProducts($categoryId, $productSkus, $sortingMode);

        $this->messageManager->addSuccessMessage(__("Products have been assigned to category."));
        return $result->setData([
            'success' => true
        ]);
    }

}
