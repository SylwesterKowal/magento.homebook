<?php

class Wm21w_Homebook_Adminhtml_HomebookbackendController extends Mage_Adminhtml_Controller_Action
{

    public $productXml = null;
    public $offersXml = null;

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Home Book XML"));
        $this->renderLayout();
        $this->exportDomodi();
    }

    public function exportDomodi()
    {
        $this->createXML();
    }

    private function getCollectionOfProducts()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('homebook', true)
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addUrlRewrite();

        return $collection;
    }


    private function createXML()
    {
        $this->productXml = new DOMDocument("1.0", "utf-8");
        $this->offersXml = $this->productXml->createElement("offers");
        $products = $this->getCollectionOfProducts();
        Mage::getSingleton('core/resource_iterator')->walk($products->getSelect(), array(array($this, 'productCallback')));
        $this->productXml->appendChild($this->offersXml);
        $this->productXml->save("homebook.xml");
    }

    /**
     * callback method
     *
     * @param $args
     */
    public function productCallback($args)
    {
        $product_ = Mage::getModel('catalog/product'); // get customer model
        $product_->setData($args['row']); // map data to customer model

        $product = Mage::getModel('catalog/product')->load($product_->getId());

        $offer = $this->productXml->createElement("offer");

        $offer->appendChild($this->cElement('id', $product->getSku()));
        $offer->appendChild($this->cElement('url', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getUrlPath()));
        $offer->appendChild($this->cElement('price', $product->getPrice()));
        $offer->appendChild($this->cElement('brand', $product->getAttributeText('manufacturer')));
        $avil = ($product->isSaleable()) ? 1 : 99;
        $offer->appendChild($this->cElement('avail', $avil));
        $offer->appendChild($this->cElement('cat', $this->cElementCategories($product)));
        $offer->appendChild($this->cElement('name', $product->getName()));
        $offer->appendChild($this->cElementGallery($product));
        $offer->appendChild($this->productXml->createElement('size',''));
        $offer->appendChild($this->productXml->createElement('IsPromoted', 0));
        $offer->appendChild($this->cElement('oldprice', ''));
        $offer->appendChild($this->cElement('desc', $product->getDescription()));
        $offer->appendChild($this->cElement('gender', 'Męśka i Damska'));
        $offer->appendChild($this->cElementAttr($product));

        $this->offersXml->appendChild($offer);
    }

    private function cElementAttr($product)
    {
        $eAttr = $this->productXml->createElement("attrs");
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {

                $attributeLabel = $attribute->getFrontendLabel();
                $value = $attribute->getFrontend()->getValue($product);

                $el = $this->productXml->createElement('attr');
                $el->setAttribute("name", $attributeLabel);
                $el->appendChild($this->productXml->createCDATASection($value));
                $eAttr->appendChild($el);
            }
        }
        return $eAttr;
    }

    private function cElementGallery($product)
    {
        $images = $this->productXml->createElement("images");
        $_images = $product->getMediaGalleryImages();

        if ($_images) {
            foreach ($_images as $image) {
                $el = $this->productXml->createElement('img');
                $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image->getFile();
                $el->appendChild($this->productXml->createCDATASection($img));
                $images->appendChild($el);
            }
        }
        return $images;
    }

    private function cElementCategories($product)
    {
        $_categories = $product->getCategoryCollection()->addAttributeToSelect('name');
        $cat = [];
        if ($_categories) {
            foreach ($_categories as $category) {
                $cat[] = $category->getName();
            }
        }
        return implode('/', $cat);
    }

    private function cElement($eName, $value)
    {
        $el = $this->productXml->createElement($eName);
        $el->appendChild($this->productXml->createCDATASection($value));
        return $el;
    }

    public function walk($query, array $callbacks, array $args = array(), $adapter = null)
    {
        $stmt = $this->_getStatement($query, $adapter);
        $args['idx'] = 0;
        while ($row = $stmt->fetch()) {
            $args['row'] = $row;
            foreach ($callbacks as $callback) {
                $r = call_user_func($callback, $args);
                if (!empty($r)) {
                    $args = array_merge($args, $r);
                }
            }
            $args['idx']++;
        }
        return $this;
    }
}