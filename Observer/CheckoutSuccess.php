<?php
/**
 * Created by PhpStorm.
 * User: rene
 * Date: 5/12/18
 * Time: 04:38 PM
 */

namespace Magmalabs\OrderSuccess\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\Manager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderNotifier;
use Magmalabs\OrderSuccess\Helper\Data;

/**
 * Class CheckoutSuccess
 * @package Magmalabs\OrderSuccess\Observer
 */
class CheckoutSuccess implements ObserverInterface
{

    /** @var Data */
    protected $helper;

    /** @var \Magento\Sales\Model\OrderNotifier $orderNotifier */
    protected $orderNotifier;

    /** @var Manager */
    protected $messageManager;

    /** @var Order */
    protected $orderModel;

    /**
     * CheckoutSuccess constructor.
     * @param Data $helper
     * @param OrderNotifier $orderNotifier
     * @param Manager $messageManager
     */
    public function __construct(Data $helper, OrderNotifier $orderNotifier, Manager $messageManager, Order $orderModel)
    {
        $this->helper = $helper;
        $this->orderNotifier = $orderNotifier;
        $this->messageManager = $messageManager;
        $this->orderModel = $orderModel;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            $orderIds  = $observer->getEvent()->getOrderIds();
            $email = $this->helper->getEmailCc();

            /** @var Order $order */
            $order = $this->getOrder($orderIds);
            $order->setCustomerEmail($email);
            if ($order) {
                try {
                    $this->orderNotifier->notify($order);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('We can\'t send order emil at this time'));
                }
            }
        }
    }

    /**
     * Get order by ids
     * @param $ids
     * @return Order
     */
    public function getOrder($ids)
    {
        $orderId   = $ids[0];

        return $this->orderModel->load($orderId);
    }
}
