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
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\Manager;
use Magento\Sales\Model\Order;
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

    /** @var TransportBuilder */
    protected $transportBuilder;

    /** @var \Magento\Store\Model\App\Emulation $emulation */
    protected $emulation;

    /**
     * CheckoutSuccess constructor.
     * @param Data $helper
     * @param OrderNotifier $orderNotifier
     * @param Manager $messageManager
     * @param Order $orderModel
     * @param TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\App\Emulation $emulation
     */
    public function __construct(
        Data $helper,
        OrderNotifier $orderNotifier,
        Manager $messageManager,
        Order $orderModel,
        TransportBuilder $transportBuilder,
        \Magento\Store\Model\App\Emulation $emulation
    ) {
        $this->helper = $helper;
        $this->orderNotifier = $orderNotifier;
        $this->messageManager = $messageManager;
        $this->orderModel = $orderModel;
        $this->transportBuilder = $transportBuilder;
        $this->emulation = $emulation;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isModuleEnabled()) {
            $orderIds  = $observer->getEvent()->getOrderIds();

            /** @var Order $order */
            $order = $this->getOrder($orderIds);
            if ($order) {
                try {
                    $emailData = $this->getEmailData($order);
                    $this->helper->sendOrderItemsEmail($emailData['templateVars'], $emailData['senderInfo'], $emailData['receiverInfo']);
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
    private function getOrder($ids)
    {
        $orderId   = $ids[0];

        return $this->orderModel->load($orderId);
    }

    /**
     * @param $order
     * @return array
     */
    private function getEmailData($order)
    {
        $emailData = [];

        $emailData['receiverInfo'] = [
            'name' => $this->helper->getStoreName(),
            'email' => $this->helper->getEmailCc()
        ];

        $emailData['senderInfo'] = [
            'name' => $this->helper->getStoreName(),
            'email' => $this->helper->getContactEmail(),
        ];

        $emailData['templateVars']['order'] = $order;

        return $emailData;
    }
}
