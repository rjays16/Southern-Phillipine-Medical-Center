<?php 
header('Content-Type: text/html; charset=ISO-8859-1');
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

if(isset($_POST["province"])){
	getProvincebyMunicipality($_POST["province"]);
}

if(isset($_POST["municipality"])){
	getMunicipalitybyProvince($_POST["municipality"]);
}

if(isset($_POST["barangay"])){
	getBarangaybyMunicipality($_POST["barangay"]);
}

if(isset($_POST["revmunicipality"])){
	getMunicipalitybyBarangay($_POST["revmunicipality"]);
}


function getProvincebyMunicipality($mun_nr){
	global $db;

	$sql = "SELECT 
				s_pro.prov_nr,
				s_pro.prov_name,
			    if((SELECT prov_nr FROM seg_municity where mun_nr = ".$db->qstr($mun_nr).") = s_muni.prov_nr, 1, 0) as selected
			FROM
			    seg_municity AS s_muni
			        LEFT JOIN
			    seg_provinces AS s_pro 
					ON s_pro.prov_nr = s_muni.prov_nr 
						group by s_pro.prov_nr 
						order by prov_name ASC";
	$result = $db->Execute($sql);
	echo '<option value="0"> Select Province </option>';
	while($row=$result->FetchRow()) {
		$selected = ($row['selected'] == 1)? " selected" : "";
		echo '<option value="'.$row['prov_nr'].'" '. $selected .'>'.$row['prov_name']."</option> \n";
	}
}


function getMunicipalitybyProvince($prov_nr){
	global $db;
	$sql = "SELECT * FROM seg_municity where prov_nr = ".$db->qstr($prov_nr)." order by mun_name ASC";
	$result = $db->Execute($sql);
	echo '<option value="0"> Select Municipality </option>';
	while($row=$result->FetchRow()) {
		echo '<option value="'.$row['mun_nr'].'" >'.$row['mun_name']."</option> \n";
	}
}


function getBarangaybyMunicipality($mun_nr){
	global $db;
	$sql = "SELECT * FROM seg_barangays where mun_nr = ".$db->qstr($mun_nr)." order by brgy_name ASC";
	$result = $db->Execute($sql);
	echo '<option value="0"> Select Barangay </option>';
	while($row=$result->FetchRow()) {
		echo '<option value="'.$row['brgy_nr'].'" >'.$row['brgy_name']."</option> \n";
	}
}


function getMunicipalitybyBarangay($brgy_nr){
	global $db;

	$sql = "SELECT 
			    s_pro.prov_nr,
			    s_pro.prov_name,
			    s_muni.mun_nr,
			    s_muni.mun_name,
			    s_bara.brgy_nr,
			    s_bara.brgy_name,
			    IF((SELECT mun_nr FROM seg_barangays WHERE brgy_nr = ". $db->qstr($brgy_nr) .") = s_muni.mun_nr, 1, 0) AS municipality_selected,
			    IF((SELECT brgy_nr FROM seg_barangays WHERE brgy_nr = ". $db->qstr($brgy_nr) .") = s_bara.brgy_nr, 1, 0) AS baranggay_selected
			FROM
			    seg_provinces AS s_pro
			        LEFT JOIN
			    seg_municity AS s_muni ON s_pro.prov_nr = s_muni.prov_nr
			        right JOIN
			    seg_barangays AS s_bara ON s_muni.mun_nr = s_bara.mun_nr
			GROUP BY s_bara.mun_nr";
	$result = $db->Execute($sql);
	echo '<option value="0"> Select Municipality </option>';
	while($row=$result->FetchRow()) {
		$selected = ($row['municipality_selected'] == 1)? " selected" : "";
		echo '<option value="'.$row['mun_nr'].'" '. $selected .'>'.$row['mun_name']."</option> \n";
	}
}
?>