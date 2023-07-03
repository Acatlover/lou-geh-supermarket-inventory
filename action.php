<?php
session_start();
include 'Inventory.php';
$inventory = new Inventory();
if(!empty($_GET['action']) && $_GET['action'] == 'logout') {
	session_unset();
	session_destroy();
	header("Location:index.php");
}
if(!empty($_POST['action']) && $_POST['action'] == 'getInventoryDetails') {
	$inventory->getInventoryDetails();
}
// Customer management
if(!empty($_POST['action']) && $_POST['action'] == 'customerList') {
	$inventory->getCustomerList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerAdd'){
	$inventory->saveCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getCustomer'){
	$inventory->getCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerUpdate'){
	$inventory->updateCustomer();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'customerDelete'){
	$inventory->deleteCustomer();
}
// Product management
if(!empty($_POST['action']) && $_POST['action'] == 'listProduct') {
	$inventory->getProductList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addProduct') {
	$inventory->addProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getProductDetails') {
	$inventory->getProductDetails();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateProduct'){
	$inventory->updateProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteProduct'){
	$inventory->deleteProduct();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'viewProduct'){
	$inventory->viewProductDetails();
}
// manage supplier
if(!empty($_POST['action']) && $_POST['action'] == 'supplierList') {
	$inventory->getSupplierList();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addSupplier'){
	$inventory->addSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getSupplier'){
	$inventory->getSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updateSupplier'){
	$inventory->updateSupplier();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deleteSupplier'){
	$inventory->deleteSupplier();
}
// manage purchase
if(!empty($_POST['action']) && $_POST['action'] == 'listPurchase') {
	$inventory->listPurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'addPurchase'){
	$inventory->addPurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'getPurchaseDetails'){
	$inventory->getPurchaseDetails();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'updatePurchase'){
	$inventory->updatePurchase();
}
if(!empty($_POST['btn_action']) && $_POST['btn_action'] == 'deletePurchase'){
	$inventory->deletePurchase();
}