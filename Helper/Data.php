<?php
/**
 * Created by PhpStorm.
 * User: rene
 * Date: 6/12/18
 * Time: 09:17 AM
 */

namespace Magmalabs\OrderSuccess\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Magmalabs\OrderSuccess\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_MAGMALABS_SHASA_DEMO_EMAIL = 'shasa_demo/general/email_cc';
    const XML_PATH_MAGMALABS_SHASA_DEMO_ENABLE = 'shasa_demo/general/enable';
    const XML_PATH_MAGMALABS_SHASA_DEMO_SUCCESS_PAGE_ENABLE = 'shasa_demo/general/enable_success_page_items';

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
}
