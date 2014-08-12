<?php 

require_once 'app/Mage.php';

Mage::app();
//$product = new Boduk_Demo_Model_Product;
//$product->sayHello();
//$customer = Mage::getModel("customer/session");
//$product = Mage::getModel("demo/product");
//$product->sayHello();
//$helper = Mage::helper("demo");
//$helper->sayHi();


/* $category = Mage::getModel("catalog/category")->load(3);

foreach($category->getChildren() as $cat ){
	$category_name = Mage::getModel("catalog/category")->load($cat);
	echo $category_name->getName();
	echo "<br>";
	
	$category_image = Mage::getModel("catalog/category")->load($cat);
	echo "<img src='".$category_image->getImageUrl()."'><br>";
	
	$category_getchildren = Mage::getModel("catalog/category")->load($cat);
		foreach($category_getchildren->getChildren() as $cat_children ){
			$category_name_child = Mage::getModel("catalog/category")->load($cat_children);
			echo $category_name_child->getName().",";
		}
	echo "<hr>";
} */

//var_dump($category_image->getImageUrl());

/* $var_dir = Mage::getModel('core/config')->getVarDir();
echo $var_dir;
 */
?>
<hr> 
<?php 

#sample get product info
//$new_product = Mage::getModel('catalog/product')->load(166);
/* echo "<pre>";
var_dump($new_product);
echo "</pre>";
 */
 
 
//get product collecttion and filter to greater than 100
 $new_product001 = Mage::getModel('catalog/product')->getCollection()
				  ->addAttributeToSelect(array('name','price'))
				  ->addFieldToFilter('price',array('gt'=>100));
 
// foreach($new_product001 as $prod){
	// echo $prod->getName().' - '.$prod->getPrice().'<br>';
// } 
 
echo "<br>";

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 







