<?php
$list_id=$_POST['list_id'];
if (!isset($list_id)) {
   echo 'list_id not set';
   exit;
}
$dblink = new mysqli(       );
if ($dblink->connect_error) {
     die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
    $query = "SELECT * FROM listName WHERE listName.list_id='$list_id'";
    $result = $dblink->query($query);
    $num_rows = $result->num_rows;

    if ($num_rows == 0) {
        echo 'not found';
		exit;
    }
	echo '{"list": ';
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);

	echo ', "items": ';
	
    $query = "SELECT * FROM list inner join listItems on list.item_id=listItems.item_id WHERE list_id='$list_id' ORDER BY item";
    $result = $dblink->query($query);
    $num_rows = $result->num_rows;

    if ($num_rows > 0) {
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		echo json_encode($rows);
	} else {
		echo '[]';
	}
	echo '}';
?>
