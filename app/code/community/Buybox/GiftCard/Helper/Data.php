<?php

class Buybox_GiftCard_Helper_Data extends Mage_Core_Helper_Abstract
{
   
    public function prepareLineItems(Mage_Core_Model_Abstract $salesEntity, $discountTotalAsItem = true, $shippingTotalAsItem = false)
    {
        $items = array();
        foreach ($salesEntity->getAllItems() as $item) {
            if (!$item->getParentItem()) {
                $items[] = new Varien_Object($this->_prepareLineItemFields($salesEntity, $item));
            }
        }
        $additionalItems = new Varien_Object(array('items'=>array()));
       
        $additionalAmount   = 0;
        $discountAmount     = 0; // this amount always includes the shipping discount
        foreach ($additionalItems->getItems() as $item) {
            if ($item['amount'] > 0) {
                $additionalAmount += $item['amount'];
                $items[] = $item;
            } else {
                $discountAmount += abs($item['amount']);
            }
        }
        $shippingDescription = '';
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $discountAmount += abs($salesEntity->getBaseDiscountAmount());
            $shippingDescription = $salesEntity->getShippingDescription();
            $totals = array(
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $salesEntity->getBaseTaxAmount(),
                'shipping' => $salesEntity->getBaseShippingAmount(),
                'discount' => $discountAmount,
                'shipping_discount' => -1 * abs($salesEntity->getBaseShippingDiscountAmount()),
            );
        } else {
            $address = $salesEntity->getIsVirtual() ? $salesEntity->getBillingAddress() : $salesEntity->getShippingAddress();
            $discountAmount += abs($address->getBaseDiscountAmount());
            $shippingDescription = $address->getShippingDescription();
            $totals = array (
                'subtotal' => $salesEntity->getBaseSubtotal() - $discountAmount,
                'tax'      => $address->getBaseTaxAmount(),
                'shipping' => $address->getBaseShippingAmount(),
                'discount' => $discountAmount,
                'shipping_discount' => -1 * abs($address->getBaseShippingDiscountAmount()),
            );
        }

        // discount total as line item (negative)
        if ($discountTotalAsItem && $discountAmount) {
            $items[] = new Varien_Object(array(
                'name'   => Mage::helper('gift_card')->__('Discount'),
                'qty'    => 1,
                'amount' => -1.00 * $discountAmount,
            ));
        }
        // shipping total as line item
        if ($shippingTotalAsItem && (!$salesEntity->getIsVirtual()) && (float)$totals['shipping']) {
            $items[] = new Varien_Object(array(
                'id'     => Mage::helper('gift_card')->__('Shipping'),
                'name'   => $shippingDescription,
                'qty'    => 1,
                'amount' => (float)$totals['shipping'],
            ));
        }

        $hiddenTax = (float) $salesEntity->getBaseHiddenTaxAmount();
        if ($hiddenTax) {
            $items[] = new Varien_Object(array(
                'name'   => Mage::helper('gift_card')->__('Discount Tax'),
                'qty'    => 1,
                'amount' => (float)$hiddenTax,
            ));
        }

        return array($items, $totals, $discountAmount, $totals['shipping']);
    }

    public function areCartLineItemsValid($items, $totals, $referenceAmount)
    {
        $sum = 0;
        foreach ($items as $i) {
            $sum = $sum + $i['qty'] * $i['amount'];
        }
        /**
         * numbers are intentionally converted to strings because of possible comparison error
         * see http://php.net/float
         */
        return sprintf('%.4F', ($sum + $totals['shipping'] + $totals['tax'])) == sprintf('%.4F', $referenceAmount);
    }

    protected function _prepareLineItemFields(Mage_Core_Model_Abstract $salesEntity, Varien_Object $item)
    {
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $qty = $item->getQtyOrdered();
            $amount = $item->getBasePrice();
            // TODO: nominal item for order
        } else {
            $qty = $item->getTotalQty();
            $amount = $item->isNominal() ? 0 : $item->getBaseCalculationPrice();
        }
        // workaround in case if item subtotal precision is not compatible with PayPal (.2)
        $subAggregatedLabel = '';
        if ((float)$amount - round((float)$amount, 2)) {
            $amount = $amount * $qty;
            $subAggregatedLabel = ' x' . $qty;
            $qty = 1;
        }
        return array(
            'id'     => $item->getSku(),
            'name'   => $item->getName() . $subAggregatedLabel,
            'qty'    => $qty,
            'amount' => (float)$amount,
        );
    }
}
