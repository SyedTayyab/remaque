<?php
class Smartwave_Legenda_Block_Checkout_Links extends Mage_Checkout_Block_Links
{
    public function addCartLink()
    {
        $parentBlock = $this->getParentBlock();
        $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals();        
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Mage_Checkout')) {           
            $subTotal = $totals['subtotal']->getValue();
            if ($subTotal > 0) {
                $text = $this->__('Shopping Bag').'&nbsp;<span class="price">'.Mage::helper('checkout')->formatPrice($subTotal).'</span>';
            } else {
                $text = $this->__('Shopping Bag').'&nbsp;<span class="price">'.Mage::helper('checkout')->formatPrice(0.00).'</span>';
            }            
            $title = 'Shopping Bag';
            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
            $parentBlock->addLink($text, 'checkout/cart', $title, true, array(), 130, null, 'class="top-link-cart"');            
        }
        return $this;
    }
}