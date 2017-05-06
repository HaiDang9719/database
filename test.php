<?php 
require_once 'users/init.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}


// $MAC = getMACaddrByWarehouseName("USA1");

// foreach ($MAC as $M) {
//     echo $M->MAC;
// }
// $RFID = "100hg";
// $pressure = 100;
// $voltage = 120;
// createRFIDTable("abcde");
// createTemperatureTable("abcd");
// $currentI = 100;
// insertRFIDTableToDatabase("abcd","100","20","30");
// $k = findRFID("1a");

// foreach ($k as $M) {
//     echo $M->Pressure;
// }

// $fk = findRFIDwithStationID("2");
// foreach ($fk as $M) {
//     echo $M->Voltage;
//}
//insertRFIDtorfidsTable("10p");
//insertRFIDtoStationTable("8391029","5","6","ok");

//createRFIDTable('R123456'); 
//insertRFIDtorfidsTable('R456345',190,200,10);
//findStationID("5","6","ok");
$json = '{"e":0,"i":{"r": 0,"d":   0},"g":{"a":[1    ],"p":[ 5000            ]},"p":[{"a":1,"d":[{"p":[ 8344      ],"r":[ 42, 14, 63,126]},{"p":[ 3855      ],"r":[250,158, 77,126]},{"p":[   60      ],"r":[  0,  0,  0,  0]},{"p":[   61      ],"r":[  0,  0,  0,  0]},{"p":[   62      ],"r":[  0,  0,  0,  0]},{"p":[   58      ],"r":[  0,  0,  0,  0]}]},{"a":2,"d":[{"p":[   30      ],"r":[ 22,108, 33,158]},{"p":[   37      ],"r":[ 86,160,  3,158]},{"p":[ 2514      ],"r":[166, 61, 53,158]},{"p":[ 2493      ],"r":[214,246, 49,158]},{"p":[ 2495      ],"r":[ 86,160, 17,158]},{"p":[ 2975      ],"r":[102,152,255,157]}]}],"m":[  0, 26,182,  3,  4, 25]}';
$result = json_decode ($json,true);
$mac_count = 0;
$WHMAC = "";
while ($mac_count <= 5) {
        $WHMAC .= dechex((float)$result['m'][$mac_count]);
        $mac_count++;
}
$i=0;
$RFID = "R";
$rfid_count = 1;
$sdcsAddr_count = 0;
$sdcsCH = 0;
$numberOfsdcsAddr = count($result['p']);
while (($sdcsAddr_count <=$numberOfsdcsAddr-1) and ($numberOfsdcsAddr >0)){
	
while ($rfid_count <=6){
	$sdcsAddr = $result['p'][$sdcsAddr_count]['a'];
	
	
	while ( $i<= 3) {
	# code...

		$RFID .= dechex((float)$result['p'][$sdcsAddr_count]['d'][$rfid_count-1]['r'][$i]);
		$i++;
	}
	$i=0;
	if ($RFID != 'R0000') {
		$sdcsCH = $rfid_count;
		//echo $sdcsCH."---sdcsCH------";
		//echo $sdcsAddr."---sdcsAddr------";
		$pressure = $result['p'][$sdcsAddr_count]['d'][$rfid_count-1]['p'][0];
		//echo $result['p'][$sdcsAddr_count]['d'][$rfid_count-1]['p'][0]."----Pressure------";
		//echo $RFID."---RFID"."\n";
		//echo $WHMAC;
		$warehouseName=getWarehouseNameByMACAddr($WHMAC);
		//echo $warehouseName;
		echo $sdcsAddr;
		echo $sdcsCH;
		insertRFIDtorfidsTable($RFID,$pressure,0,0);
		insertRFIDtoStationTable($RFID,$sdcsAddr,$sdcsCH,$warehouseName);

		
	}
	$RFID = "R";
	
	$rfid_count ++;
}
	$rfid_count = 1;
	$RFID = "R";
$sdcsAddr_count++;
}


//echo $WHMAC."----WHMac";

?>
