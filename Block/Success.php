<?php
/**
 * Created by PhpStorm.
 * User: rene
 * Date: 7/12/18
 * Time: 12:46 PM
 */

namespace Magmalabs\OrderSuccess\Block;


/**
 * Class Success
 * @package Magmalabs\OrderSuccess\Block
 */
class Success extends  \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}
