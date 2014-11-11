<?php

//get connection to db


$upc=$_GET['upc'];


$query1="SELECT * FROM upcData WHERE upc=$upc";

$result = $dblink->query($query1);


$num_rows = $result->num_rows;
 
if (!$result) {
	echo "Could not successfully run query ($query) from DB: " . mysql_error();
	exit;
}
if ($num_rows > 0) {
	//found in db2g database - return it
	$rows = $result->fetch_array();
	echo json_encode($rows);
	exit;
} else {

	//not in our database - lookup somewhere else

	$url = 'http://api.v3.factual.com/t/products-cpg?filters={"upc":"'.$upc.'"}&KEY=kp5lqLW5FzQzJ31o5B2idRd6KGvhhuvqRbIyHiec&select=brand,product_name,size,manufacturer,avg_price,category';

	$json = file_get_contents($url,0,null,null);
	$json_output = json_decode($json);
	$rows = $json_output->response->included_rows;

	if ($rows>0) {
		//found in external db

		$desc = $json_output->response->data[0]->brand . ' ' . $json_output->response->data[0]->product_name;
		$desc = $dblink->real_escape_string($desc);
		$size = $dblink->real_escape_string($json_output->response->data[0]->size[0]);
		$category = $dblink->real_escape_string($json_output->response->data[0]->category);
		$company = $dblink->real_escape_string($json_output->response->data[0]->manufacturer);
		if (strpos($size,' ')) {
			$pieces = explode(' ',$size,2);
			$qty = $pieces[0];
			$unit = $pieces[1];
			if (!is_numeric($qty)) {
				$qty = 0;
				$unit = '';
			}
		} else {
			$qty=0;
			$unit='';
		}
		$query2 = 'INSERT INTO upcData SET upc='.$upc.', description="'.$desc.'", size="'.$size.'", qty='.$qty.', uom="'.$unit.'", company="'.$company.'"';
		$result = $dblink->query($query2);
	} else {
		//not found in local db or external db, create blank record for now
		$query2 = 'INSERT INTO upcData SET upc='.$upc;
		$result = $dblink->query($query2);
	}

	//new record created above - go get it
	$result = $dblink->query($query1);
	
}
$rows = $result->fetch_array(MYSQLI_ASSOC);
echo json_encode($rows);
?>