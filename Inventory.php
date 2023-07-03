<?php
class Inventory {
    private $host  = 'localhost';
    private $user  = 'root';
    private $password   = '';
    private $database  = 'supermarket';   
    private $customerTable = 'customer';	
	private $productTable = 'product';
	private $supplierTable = 'supplier';
	private $purchaseTable = 'purchase';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function getCustomer(){
		$sqlQuery = "
			SELECT * FROM ".$this->customerTable." 
			WHERE id = '".$_POST["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	
	public function getCustomerList(){		
		$sqlQuery = "SELECT * FROM ".$this->customerTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= '(name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR address LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$customerData = array();	
		while( $customer = mysqli_fetch_assoc($result) ) {		
			$customerRows = array();
			$customerRows[] = $customer['id'];
			$customerRows[] = $customer['name'];
			$customerRows[] = $customer['address'];			
			$customerRows[] = $customer['mobile'];	
			$customerRows[] = '<button type="button" name="update" id="'.$customer["id"].'" class="btn btn-primary btn-sm rounded-0 update" title="update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$customer["id"].'" class="btn btn-danger btn-sm rounded-0 delete" ><i class="fa fa-trash"></button>';
			$customerRows[] = '';
			$customerData[] = $customerRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$customerData
		);
		echo json_encode($output);
	}

	public function saveCustomer() {		
		$sqlInsert = "
			INSERT INTO ".$this->customerTable."(name, address, mobile) 
			VALUES ('".$_POST['name']."', '".$_POST['address']."', '".$_POST['mobile']."')";		
		mysqli_query($this->dbConnect, $sqlInsert);
		echo 'New Customer Added';
	}			
	public function updateCustomer() {
		if($_POST['userid']) {	
			$sqlInsert = "
				UPDATE ".$this->customerTable." 
				SET name = '".$_POST['name']."', address= '".$_POST['address']."', mobile = '".$_POST['mobile']."' 
				WHERE id = '".$_POST['userid']."'";		
			mysqli_query($this->dbConnect, $sqlInsert);	
			echo 'Customer Edited';
		}	
	}	
	public function deleteCustomer(){
		$sqlQuery = "
			DELETE FROM ".$this->customerTable." 
			WHERE id = '".$_POST['userid']."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// Product management 
	public function getProductList(){				
		$sqlQuery = "SELECT * FROM ".$this->productTable." as p ";
		if(isset($_POST["search"]["value"])) {
			$sqlQuery .= 'WHERE p.quantity LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR p.pid LIKE "%'.$_POST["search"]["value"].'%" ';
		}
		if(isset($_POST['order'])) {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY p.pid DESC ';
		}
		if($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$productData = array();	
		while( $product = mysqli_fetch_assoc($result) ) {			
			$status = '';
			if($product['status'] == 'active') {
				$status = '<span class="label label-success">Active</span>';
			} else {
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$productRow = array();
			$productRow[] = $product['pid'];
			$productRow[] = $product['barcode'];
			$productRow[] = $product['description'];		
			$productRow[] = $product["quantity"];
			$productRow[] = $product["cost_per_unit"];
			$productRow[] = $status;
			$productRow[] = '<button type="button" name="update" id="'.$product["pid"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$product["pid"].'" class="btn btn-danger btn-sm rounded-0  delete"><i class="fa fa-trash"></i></button></div>';
			$productData[] = $productRow;
						
		}
		$outputData = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$productData
		);
		echo json_encode($outputData);
	}
	public function addProduct() {		
		$sqlInsert = "
			INSERT INTO ".$this->productTable."(barcode,  description, quantity, cost_per_unit) 
			VALUES ('".$_POST["barcode"]."', '".$_POST['description']."', '".$_POST['quantity']."', '".$_POST['cost_per_unit']."')";		
		mysqli_query($this->dbConnect, $sqlInsert);
		echo 'New Product Added';
	}	
	public function getProductDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->productTable." 
			WHERE pid = '".$_POST["pid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);			
		while( $product = mysqli_fetch_assoc($result)) {
			$output['pid'] = $product['pid'];
			$output['barcode'] = $product['barcode'];
			$output['description'] = $product['description'];
			$output['quantity'] = $product['quantity'];
			$output['cost_per_unit'] = $product['cost_per_unit'];
		}
		echo json_encode($output);
	}
	public function updateProduct() {		
		if($_POST['pid']) {	
			$sqlUpdate = "UPDATE ".$this->productTable." 
				SET barcode = '".$_POST['barcode']."', description='".$_POST['description']."', quantity='".$_POST['quantity']."', cost_per_unit='".$_POST['cost_per_unit']."' WHERE pid = '".$_POST["pid"]."'";			
			mysqli_query($this->dbConnect, $sqlUpdate);	
			echo 'Product Update';
		}	
	}	
	public function deleteProduct(){
		$sqlQuery = "
			DELETE FROM ".$this->productTable." 
			WHERE pid = '".$_POST["pid"]."'";	
		mysqli_query($this->dbConnect, $sqlQuery);		
	}	
	// supplier 
	public function getSupplierList(){		
		$sqlQuery = "SELECT * FROM ".$this->supplierTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'WHERE (company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= '(address LIKE "%'.$_POST["search"]["value"].'%" ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY code DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$supplierData = array();	
		while( $supplier = mysqli_fetch_assoc($result) ) {	
			$status = '';
			if($supplier['status'] == 'active') {
				$status = '<span class="label label-success">Active</span>';
			} else {
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$supplierRows = array();
			$supplierRows[] = $supplier['code'];		
			$supplierRows[] = $supplier['company_name'];	
			$supplierRows[] = $supplier['mobile'];			
			$supplierRows[] = $supplier['address'];	
			$supplierRows[] = $status;			
			$supplierRows[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$supplier["code"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$supplier["code"].'" class="btn btn-danger btn-sm rounded-0  delete"  title="Delete"><i class="fa fa-trash"></i></button></div>';
			$supplierData[] = $supplierRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$supplierData
		);
		echo json_encode($output);
	}
	public function addSupplier() {		
		$sqlInsert = "
			INSERT INTO ".$this->supplierTable."(company_name, mobile, address) 
			VALUES ('".$_POST['company_name']."', '".$_POST['mobile']."', '".$_POST['address']."')";		
		mysqli_query($this->dbConnect, $sqlInsert);
		echo 'New Supplier Added';
	}			
	public function getSupplier(){
		$sqlQuery = "
			SELECT * FROM ".$this->supplierTable." 
			WHERE code = '".$_POST["code"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updateSupplier() {
		if($_POST['code']) {	
			$sqlUpdate = "
				UPDATE ".$this->supplierTable." 
				SET company_name = '".$_POST['company_name']."', mobile= '".$_POST['mobile']."' , address= '".$_POST['address']."'	WHERE code = '".$_POST['code']."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);	
			echo 'Supplier Edited';
		}	
	}	
	public function deleteSupplier(){
		$sqlQuery = "
			DELETE FROM ".$this->supplierTable." 
			WHERE code = '".$_POST['code']."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// purchase
	public function listPurchase(){		
		$sqlQuery = "SELECT ph.*, p.barcode FROM ".$this->purchaseTable." as ph 
			INNER JOIN ".$this->productTable." as p ON p.pid = ph.pid ";
		if(isset($_POST['order'])) {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY ph.purchase_id DESC ';
		}
		if($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$purchaseData = array();	
		while( $purchase = mysqli_fetch_assoc($result) ) {			
			$productRow = array();
			$productRow[] = $purchase['purchase_id'];
			$productRow[] = $purchase['barcode'];
			$productRow[] = $purchase['quantity'];			
			$productRow[] = $purchase['unit_price'];
			$productRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$purchase["purchase_id"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$purchase["purchase_id"].'" class="btn btn-danger btn-sm rounded-0  delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
			$purchaseData[] = $productRow;
						
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$purchaseData
		);
		echo json_encode($output);		
	}
	public function productDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->productTable." ORDER BY barcode ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $product = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$product["pid"].'">'.$product["barcode"].'</option>';
		}
		return $dropdownHTML;
	}
	public function addPurchase() {		
		$sqlInsert = "
			INSERT INTO ".$this->purchaseTable."(pid, quantity, unit_price) 
			VALUES ('".$_POST['pid']."', '".$_POST['quantity']."', '".$_POST['unit_price']."')";		
		mysqli_query($this->dbConnect, $sqlInsert);
		echo 'New Purchase Added';
	}	
	public function getPurchaseDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->purchaseTable." 
			WHERE purchase_id = '".$_POST["purchase_id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updatePurchase() {
		if($_POST['purchase_id']) {	
			$sqlUpdate = "
				UPDATE ".$this->purchaseTable." 
				SET pid = '".$_POST['pid']."', quantity= '".$_POST['quantity']."', unit_price= '".$_POST['unit_price']."'	WHERE purchase_id = '".$_POST['purchase_id']."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);	
			echo 'Purchase Edited';
		}	
	}	
	public function deletePurchase(){
		$sqlQuery = "
			DELETE FROM ".$this->purchaseTable." 
			WHERE purchase_id = '".$_POST['purchase_id']."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	public function customerDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->customerTable." ORDER BY name ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $customer = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$customer["id"].'">'.$customer["name"].'</option>';
		}
		return $dropdownHTML;
	}
	public function getInventoryDetails(){		
		$sqlQuery = "SELECT p.pid, p.barcode, p.quantity as product_quantity, s.quantity as recieved_quantity, r.total_shipped
			FROM ".$this->productTable." as p
			LEFT JOIN ".$this->purchaseTable." as s ON s.pid = p.pid ";		
		if(isset($_POST['order'])) {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY p.pid DESC ';
		}
		if($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$inventoryData = array();	
		$i = 1;
		while( $inventory = mysqli_fetch_assoc($result) ) {	

			if(!$inventory['recieved_quantity']) {
				$inventory['recieved_quantity'] = 0;
			}
			if(!$inventory['total_shipped']) {
				$inventory['total_shipped'] = 0;
			}
			
			$inventoryInHand = ($inventory['product_quantity'] + $inventory['recieved_quantity']) - $inventory['total_shipped'];
		
			$inventoryRow = array();
			$inventoryRow[] = $i++;
			$inventoryRow[] = "<div class='lh-1'><div>{$inventory['barcode']}</div><div class='fw-bolder text-muted'><small>{$inventory['model']}</small></div></div>";
			$inventoryRow[] = $inventory['product_quantity'];
			$inventoryRow[] = $inventory['recieved_quantity'];	
			$inventoryRow[] = $inventory['total_shipped'];
			$inventoryRow[] = $inventoryInHand;			
			$inventoryData[] = $inventoryRow;						
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$inventoryData
		);
		echo json_encode($output);		
	}
}
?>