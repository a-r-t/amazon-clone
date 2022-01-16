<?php
require_once('model/database.php');
require_once('model/product_db.php');

// using PEAR XML Serializer Library
// https://pear.php.net/package/XML_Serializer/
require_once('XML/Serializer.php');

// process request params
// if action param is present...
if (isset($_GET['action'])) {
	if ($_GET['action'] === 'products') {
		if (isset($_GET['name'])) {
			$products = ProductDB::getProductsByName($_GET['name'], $order_by="id");
		}
		else if (isset($_GET['price_max'])) {
			$products = ProductDB::getProductsByPriceRange($_GET['price_max'], $order_by="id");
		}
		else {
			$products = ProductDB::getProducts($order_by="id");
		}
		
		if (!$products) {
			error("No products found with matching criteria");
		}
		else {
			// convert list of courses into a serializable array 
			$productsSerializable = PrepareSerializableObjArray($products);
			
			// export either xml or json based on format type
			export($productsSerializable, $rootName="products", $type="product");
		}
	}
	else {
		error(sprintf("action param '%s' not recognized", $_GET['action']));
	}
}
else {
	error("action param not found");
}

/*
 * converts an array of objects into a serializble array
 * the toDict() function added to the course and student object create an array easily convertable to json or xml
*/
function PrepareSerializableObjArray($objArray) {
	$serializedObjArray = array();
	foreach ($objArray as $obj) {
		$serializedObjArray[] = $obj->toDict();
	}
	return $serializedObjArray;
}

/*
 * exports response back to requester
 * can format in either json or xml based on param format_type (defaults to JSON if param not found)
 * $serilizable is an associative array
 *
 * $rootName and type are required for xml only
 * $rootName is for the root tags and type is for the object tags
*/
function export($serializable, $rootName="", $type="") {
	if (!isset($_GET['format_type']) || strtolower($_GET['format_type']) == 'json') {
		header('Content-Type: application/json');
		// love that php has an easy json_encode function :)
		print_r(json_encode($serializable, JSON_PRETTY_PRINT));
	}
	else if (strtolower($_GET['format_type']) == 'xml') {
		// uses PEAR XML Serializer library
		
		// array of serializer options
		$serializer_options = array (
		  'indent' => '  ',
		  'rootName' => $rootName,
		  'defaultTagName' => $type
		); 

		// create xml
		$Serializer = new XML_Serializer($serializer_options);
		$status = $Serializer->serialize($serializable);

		header('Content-Type: application/xml');
		// display xml
		echo $Serializer->getSerializedData();
	}
	else {
		error(sprintf("format_type param '%s' not recognized", $_GET['format_type']));
	}
}

// return error message in JSON format
function error($message) {
	header('Content-Type: application/json');
	echo json_encode(array("Error" => $message), JSON_PRETTY_PRINT);
}

?>