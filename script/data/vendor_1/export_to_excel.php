<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "excel_reader.php";
  if (isset($_GET['p']))
      $project_id=mysql_real_escape_string($_GET['p']);
  else
      $project_id=$_SESSION['ses_project'];	  
  $field=mysql_real_escape_string($_GET['f']);
  $operator=mysql_real_escape_string($_GET['o']);
  $text_to_find=htmlspecialchars($_GET['t']);
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find."'";	
  
  if ($text_to_find!='') 
      $q_get_vendor="SELECT vend_id, vend_code, CONCAT(vendt_name,'. ',vend_name) as vend_name, vend_address, vend_phone_no, vend_fax_no
                     FROM vendor
				     INNER JOIN vendor_title ON vendor.vendt_id=vendor_title.vendt_id
                     WHERE prj_id='$project_id' AND $find_text";
  else
      $q_get_vendor="SELECT vend_id, vend_code, CONCAT(vendt_name,'. ',vend_name) as vend_name, vend_address, vend_phone_no, vend_fax_no
                     FROM vendor
				     INNER JOIN vendor_title ON vendor.vendt_id=vendor_title.vendt_id
                     WHERE prj_id='$project_id'";
  $exec_get_vendor=mysql_query($q_get_vendor, $db_connection);
  if (mysql_num_rows($exec_get_vendor)>0)
     {
	   $filename='List_Of_Vendor.xls';
       $header="LIST OF VENDOR"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Vendor_Code"."\t"."Vendor_Name"."\t"."Vendor_Address"."\t"."Phone_No"."\t"."Fax_No"."\n"; 
	   while ($field_data=mysql_fetch_array($exec_get_vendor))
			 {
			   $rows_data=$rows_data.$field_data['vend_code']."\t".$field_data['vend_name']."\t".$field_data['vend_address']."\t".$field_data['vend_phone_no']."\t".$field_data['vend_fax_no']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Vendor.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('No Record Found!');
			   window.location.href='javascript:history.back(1)';
			</script>
		<?php
	 } 	   
?>