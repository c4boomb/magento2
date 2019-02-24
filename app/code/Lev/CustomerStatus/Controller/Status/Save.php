<?php

namespace Lev\CustomerStatus\Controller\Status;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 * @package Lev\CustomerStatus\Controller\Status
 */
class Save extends Action
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Save constructor
     *
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param ManagerInterface $messageManager
     * @param Session $customerSession
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        ManagerInterface $messageManager,
        Session $customerSession,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository
    )
    {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $newStatus = $this->getRequest()->getParam('status', false);

        if (!$this->formKeyValidator->validate($this->getRequest()) || !$newStatus || !$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage('Please try again');
        } else {
            $currentCustomer = $this->customerRepository->getById(
                $this->customerSession->getCustomerId()
            );

            $currentCustomer->setCustomAttribute('customer_status', $newStatus);

            try {
                $this->customerRepository->save($currentCustomer);

                $this->messageManager->addSuccessMessage('Status changed successfully');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage('Something went wrong, please try again later');
                $this->logger->error($e->getMessage());
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}