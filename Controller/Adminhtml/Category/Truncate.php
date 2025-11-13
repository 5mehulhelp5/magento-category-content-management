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

class Truncate extends Action implements HttpPostActionInterface
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
                'message' => __('Cannot remove all product from category, missing category id.')->render(),
                'success' => false
            ]);
        }

        $this->categoryProductLinkManagement->truncateProducts($categoryId);
        $this->messageManager->addSuccessMessage(__("Products have been removed from category."));
        return $result->setData([
            'success' => true
        ]);
    }

}
