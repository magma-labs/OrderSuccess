<?php
/**
 * Created by PhpStorm.
 * User: rene
 * Date: 6/12/18
 * Time: 09:17 AM
 */

namespace Magmalabs\OrderSuccess\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManager;

/**
 * Class Data
 * @package Magmalabs\OrderSuccess\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_MAGMALABS_SHASA_DEMO_EMAIL = 'shasa_demo/general/email_cc';
    const XML_PATH_MAGMALABS_SHASA_DEMO_ENABLE = 'shasa_demo/general/enable';
    const XML_PATH_MAGMALABS_SHASA_DEMO_SUCCESS_PAGE_ENABLE = 'shasa_demo/general/enable_success_page_items';
    const XML_PATH_MAGMALABS_CONTACT_EMAIL = 'trans_email/ident_support/email';
    const XML_PATH_MAGMALABS_STORE_NAME = 'general/store_information/name';
    const ORDER_SUCCESS_ITEMS_TEMPLATE_ID = 'after_success_order';

    /** @var TransportBuilder $transportBuilder */
    protected $transportBuilder;

    /** @var StoreManager $storeManager */
    protected $storeManager;

    /** @var StateInterface $inlineTranslation */
    protected $inlineTranslation;

    /** @var ProductRepository $productRepository */
    protected $productRepository;

    /** @var \Magento\Framework\Pricing\Helper\Data */
    protected $priceHelper;


    /**
     * Data constructor.
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StoreManager $storeManager
     * @param StateInterface $inlineTranslation
     * @param ProductRepository $productRepository
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManager $storeManager,
        StateInterface $inlineTranslation,
        ProductRepository $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->productRepository = $productRepository;
        $this->priceHelper = $priceHelper;
        parent::__construct($context);
    }

    /**
     * Get system config values
     * @param  string       $field
     * @param  null || int  $storeId
     * @return string
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function isModuleEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGMALABS_SHASA_DEMO_ENABLE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function isSuccessPageRenderEnable($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGMALABS_SHASA_DEMO_SUCCESS_PAGE_ENABLE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getEmailCc($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGMALABS_SHASA_DEMO_EMAIL, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getContactEmail($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGMALABS_CONTACT_EMAIL, $storeId);
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getStoreName($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGMALABS_STORE_NAME, $storeId);
    }

    /**
     * @param $templateVars
     * @param $senderInfo
     * @param $receiverInfo
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendOrderItemsEmail($templateVars, $senderInfo, $receiverInfo)
    {
        $this->inlineTranslation->suspend();
        $this->generateTemplate($templateVars, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }

    /**
     * @param $templateVars
     * @param $senderInfo
     * @param $receiverInfo
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateTemplate($templateVars, $senderInfo, $receiverInfo)
    {
        $template = $this->transportBuilder->setTemplateIdentifier($this->getTemplateId())
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($templateVars)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return self::ORDER_SUCCESS_ITEMS_TEMPLATE_ID;
    }

    /**
     * @param int $id Product id
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }

    /**
     * @param mixed price
     * @return boolean
     */
    public function getFormatedPrice($price='')
    {
        return $this->priceHelper->currency($price, true, false);
    }

}
