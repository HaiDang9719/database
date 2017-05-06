<?php
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//Put your custom functions in this file and they will be automatically included.

//bold("<br><br>custom helpers included");

//*** FUNCTIONS TO SUPPORT FLIPPER *****///
function createTemperatureTable($tablename)
{
	 $db = DB::getInstance();

	$value = '';
	$sql = "CREATE TABLE IF NOT EXISTS $tablename (`Time` DATETIME NOT NULL, `Temperature` DOUBLE NULL, PRIMARY KEY (`Time`)) ENGINE = InnoDB";
	
	if (!$db->query($sql, $value)->error())
	{
	
		return true;
	}

	return false;
	
}

function insertTemperatureToDatabase($tablename, $temperature)
{
	$db = DB::getInstance();


	if(!$db->tableExists($tablename))
	{
		createTemperatureTable($tablename);
	}
	
	$updateDate = date('Y-m-d H:i:s');
	$dataArray = array('Time'=> $updateDate, 'Temperature' =>$temperature);
	
	$db->insert($tablename, $dataArray);
}


/**** FUNCTIONS TO SUPPORT IOT EGUN *****/


//Retrieve information for all users
function fetchAllWarehouses() {
	$db = DB::getInstance();
	$query = $db->query("SELECT * FROM warehouses");
	$results = $query->results();
	return ($results);
}

//Delete warehouses; array
function deleteWarehouses($warehouses) {
	$db = DB::getInstance();
	$i = 0;
	foreach($warehouses as $warehousename){
		$query1 = $db->query("DELETE FROM warehouses WHERE warehousename = ?",array($warehousename));
		$i++;
	}
	return $i;
}


// get warehouseName by MAC addr

function getWarehouseNameByMACAddr($MAC)
{
	$db = DB::getInstance();
	$query1 = $db->query("SELECT warehouseName FROM warehouses WHERE MAC = ?",array($MAC));
	$result1 = $query1->results();
	foreach($result1 as $a){ $num = $a->warehouseName;echo $num;}
	return $num;
	//echo $query->results();

}

function getMACaddrByWarehouseName($Name)
{

	 $db = DB::getInstance();

	
	$sql = "SELECT * FROM warehouses WHERE warehouseName = ? ";
	$query = $db ->get('warehouses',['warehouseName','=',$Name],false);

	return $query->results();
}

// create RFID Table to Database
function createRFIDTable($tablename)
{
         $db = DB::getInstance();

        $value = '';
        $sql = "CREATE TABLE IF NOT EXISTS $tablename (
  `Time` DATETIME NOT NULL,
  `Pressure` DOUBLE NULL,
  `Voltage` DOUBLE NULL,
  `Current` DOUBLE NULL,
  PRIMARY KEY (`Time`),
  UNIQUE INDEX `Time_UNIQUE` (`Time` ASC))
ENGINE = InnoDB";

        if (!$db->query($sql, $value)->error())
        {

                return true;
        }

	return false;

}


// Insert data to RFID Table in Database
function insertRFIDTableToDatabase($RFID,$pressure,$voltage,$currentI)
{
	$db = DB::getInstance();
	//if(!$db->tableExists($RFID))
	//{
	//	createRFIDTable($RFID);
	//}
	$updateDate = date('Y-m-d H:i:s');
	$dataArray = array('Time'=> $updateDate,'Pressure' =>$pressure,'Voltage'=>$voltage,'Current'=>$currentI);
	
	$db->insert($RFID, $dataArray);
	

}
//Find RFID Table base on RFID
function findRFID($RFID)
{
	$db = DB::getInstance();
	$query = $db->query("SELECT * FROM $RFID");
        return $query->results();
}


//Find RFID Table base on StationID
function findRFIDwithStationID($StationID)
{
	$db = DB::getInstance();
	$query1 = $db->query("SELECT RFID FROM stations WHERE id=?",array($StationID));
	//$query1 = $db ->get('stations',['id','=',$StationID],false);
	$results=$query1->results();
	foreach($results as $a){ $rfid=$a->RFID; return(findRFID($rfid));}
}

//Find StationID 
function findStationID($sdcsAddr,$sdcsCH,$warehouseName)
{
	$db = DB::getInstance();
	$query1 = $db->query("SELECT id FROM stations WHERE sdcsAddr = ? AND sdcsCH = ? AND warehouseName = ?  ",[$sdcsAddr,$sdcsCH,$warehouseName]);
        $result1 = $query1->results();
        foreach($result1 as $a){ $num = $a->id;echo $num;}

}
//Insert RFID to rfids Table
function insertRFIDtorfidsTable($RFID,$pressure,$voltage,$currentI){
	$db = DB::getInstance();
	$sql = $db->query("SELECT count(*) as total  FROM rfids WHERE RFID = ?",array($RFID));
	$result1 = $sql->results();
	foreach($result1 as $a){$num = $a->total;}
	if ($num ==0){
	$field = array('RFID'=>$RFID);
        $db ->insert('rfids',$field);
	createRFIDTable($RFID);
	insertRFIDTableToDatabase($RFID,$pressure,$voltage,$currentI);
	}else{
		insertRFIDTableToDatabase($RFID,$pressure,$voltage,$currentI);
	}


}

//Override RFID in stations Table
function insertRFIDtoStationTable($RFID,$sdcsAddr,$sdcsCH,$warehouseName)
{
	$db = DB::getInstance();
	$query1 = $db->query("SELECT id FROM stations WHERE sdcsAddr = ? AND sdcsCH = ? AND warehouseName = ?  ",[$sdcsAddr,$sdcsCH,$warehouseName]);
	$result1 = $query1->results();
	foreach($result1 as $a){ $num = $a->id;}
	$field = array('RFID'=>$RFID,'sdcsAddr'=>$sdcsAddr,'sdcsCH'=>$sdcsCH,'warehouseName'=>$warehouseName);
	$db->update('stations',$num,$field);

}
