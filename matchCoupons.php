<?php
echo '<div class="couponBox">';

	//exclude words in the stopWords list;
        $query2 = "SELECT word from stopWords";
       	$result2 = $dblink->query($query2);
	$stopWords = array();
	while ($row = $result2->fetch_assoc()) {
		array_push($stopWords, $row['word']);
	}

        $query3 = "SELECT couponid,description,image,value from coupons ORDER BY RAND()";
       	$result3 = $dblink->query($query3);
	$coupons = array();
	$allWords = array();
	$i = 0;
	while ($row = $result3->fetch_assoc()) {
		$couponid = $row['couponid'];
		$description = $row['description'];
		if(substr($description,0,1)=='$') {
			$description = substr($description,stripos($description,' '));
		}
		$image = $row['image'];
		$desc = strtolower($description);
		$value = $row['value'];
		if ($value < 1) {
			$txtValue = number_format($value*100,0).'¢';
		} else {
			$txtValue = '$'.number_format($value,2);
		}
		$keywords = explode(" ", $desc);
		$keywords = $keywords = array_diff($keywords, $stopWords);
		$keywords = array_filter($keywords,function ($value) {return strlen($value) >= 4;});
		foreach ($keywords as $word) {
			
		        //get list of products user buys;
			$query4 = "SELECT DISTINCT id FROM products WHERE user_id=$user_id AND lower(product) LIKE '%$word%'";
	        	$result4 = $dblink->query($query4);
			if ( $result4->num_rows > 0 ) {
				$i=$i+1;
				echo '<div class="coupon" onClick="showCoupons('.$couponid.')"><table><tr><td><img src="'.$image.'"></td><td><span class="save">SAVE&nbsp;'.$txtValue.'</span><span class="save2"><br><br>'.$description.'</span></td></tr></table></div> ';
				//array_push($coupons,$couponid);
				//array_push($allWords,$word);
				if ($i<7){
					break 1;
				} else {
					break 2;
				}
			}
		}
	}

	$result3->data_seek(0);
	while (($row = $result3->fetch_assoc()) && ($i<7)) {
		$couponid = $row['couponid'];
		if (!in_array($couponid,$coupons)) {
			$description = $row['description'];
			if(substr($description,0,1)=='$') {
				$description = substr($description,stripos($description,' '));
			}
			$image = $row['image'];
			$desc = strtolower($description);
			$value = $row['value'];
			if ($value < 1) {
				$txtValue = number_format($value*100,0).'¢';
			} else {
				$txtValue = '$'.number_format($value,2);
			}
			echo '<div class="coupon" onClick="showCoupons('.$couponid.')"><table><tr><td><img src="'.$image.'"></td><td><span class="save">SAVE&nbsp;'.$txtValue.'</span><span class="save2"><br><br>'.$description.'</span></td></tr></table></div> ';
		}
		$i++;
	}

echo '</div>';
?>