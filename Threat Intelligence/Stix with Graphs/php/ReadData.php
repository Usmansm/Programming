<?php
	include 'reader.php';
    	$excel = new Spreadsheet_Excel_Reader();
	?>
<?php
error_reporting(E_STRICT);
	set_time_limit(0);
	$columnObj = array();
	$excel->read('data.xls'); // set the excel file name here   
	$x=2;
	for($columnCount =1 ; $columnCount < $excel->sheets[0]['numCols'];$columnCount++)
	{
		 array_push($columnObj,$excel->sheets[0]['cells'][1][$columnCount]);
	}
	$columnlength = count($columnObj);
	//echo json_encode($columnObj);
	$data = array();$months =  array();
	$monthKey = 'Timestamp';
	while($x<=$excel->sheets[0]['numRows']) { // reading row by row 
	  $y=1;
	  for($colCount =0 ; $colCount< $columnlength;$colCount++){
		  $key = $columnObj[$colCount];
		  if($key == $monthKey){
		     $monthValue = $excel->sheets[0]['cells'][$x][$colCount+1];
			$datestr = explode('T',$monthValue)[0];
			$newformat = date_parse($datestr);
			$monthYrDisplay = strftime("%b", mktime(0, 0, 0, $newformat["month"])).'-'.$newformat["year"];
			$monthYr = $newformat["month"].'_'.$newformat["year"];
			$dataObj['monthYr'] = $monthYr;
			$dataObj['monthYrDisplay'] = $monthYrDisplay;
			if(count($months)>0)
			{
					if(!checkKeyExist($monthYr,$months)){
						$rec["monthYrDisplay"] = $monthYrDisplay;
						$rec["monthYr"] = $monthYr;
						array_push($months,$rec);
					}
			}else{
				$rec["monthYrDisplay"] = $monthYrDisplay;
				$rec["monthYr"] = $monthYr;
				array_push($months,$rec);
			}
			//echo json_encode($months);
		  }
		  $dataObj[$key] = $excel->sheets[0]['cells'][$x][$colCount+1];
	  }
	  array_push($data,$dataObj);
	  $x++;
	}
	$requiredata = array();
	
	//$months1 = array_unique($months);
	//echo json_encode($months);
	$monthCount = 0;
	for($mCount =0 ;$mCount <count($months);$mCount++){
			$monvalue = $months[$mCount];
			//echo json_encode($monvalue);
			$datacount = 0 ;
			$monthDisplayName = "";
			for($i =0 ; $i < count($data); $i++)
			{
				if($data[$i]["monthYr"] == $monvalue["monthYr"]){
					$monthDisplayName = $data["monthYrDisplay"];
					$datacount++;
				}
			}
			//echo $datacount;
		//  $rec["monthYrDisplay"] = $monthYrDisplay;
		  //$rec["monthYr"] = $monthYr;
		   $months[$mCount]["count"] = $datacount;
		 // array_push($requiredata,$rec);
	}
	function checkKeyExist($monthYr,$months){
		$matchIndex = false;
		for($mCount =0 ;$mCount <count($months);$mCount++){
			if($months[$mCount]["monthYr"] == $monthYr)
			{
					return true;
			}
		}
		return $matchIndex;
	}
	echo json_encode($months);
?> 