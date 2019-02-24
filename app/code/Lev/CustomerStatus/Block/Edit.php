<?php

namespace Lev\CustomerStatus\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Edit
 * @package Lev\CustomerStatus\Block
 */
class Edit extends Template
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * Edit constructor
     *
     * @param Template\Context $context
     * @param UrlInterface $url
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $url,
        FormKey $formKey,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->url = $url;
        $this->formKey = $formKey;
    }

    /**
     * Get url for save action
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->url->getUrl('cstatus/status/save');
    }

    /**
     * Get Form key to protect form from CSRF
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}