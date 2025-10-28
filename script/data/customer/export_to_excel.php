<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find=htmlspecialchars($_GET['t']);
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find."'";	
  
  if ($text_to_find!='') 
      $q_get_customer="SELECT cust_id, cust_code, cust_name, cust_address, 
			                  CASE cust_status 
                                   WHEN '0' THEN 'Active'
                                   WHEN '1' THEN 'InActive'
                              END cust_status, cust_phone, cust_fax, cust_email
                       FROM customer
				       WHERE cust_type='0' AND branch_id='$branch_id' AND $find_text";
  else
      $q_get_customer="SELECT cust_id, cust_code, cust_name, cust_address, 
			                  CASE cust_status 
                                   WHEN '0' THEN 'Active'
                                   WHEN '1' THEN 'InActive'
                              END cust_status, cust_phone, cust_fax, cust_email
                       FROM customer
				       WHERE cust_type='0' AND branch_id='$branch_id'";
  $exec_get_customer=mysqli_query($db_connection,$q_get_customer);
  if (mysqli_num_rows($exec_get_customer)>0)
     {
	   $filename='List_Of_Customer.xls';
       $header="DAFTAR CUSTOMER"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kode Customer"."\t"."Nama Customer"."\t"."Alamat"."\t"."Status"."\t"."No Telpon"."\t"."Fax No"."\t"."Email"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_customer))
			 {
			   $rows_data=$rows_data.$field_data['cust_code']."\t".$field_data['cust_name']."\t".$field_data['cust_address']."\t".$field_data['cust_status']."\t".$field_data['cust_phone']."\t".$field_data['cust_fax']."\t".$field_data['cust_email']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Customer.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ditemukan!');
			   window.location.href='javascript:history.back(1)';
			</script>
		<?php
	 } 	   
?>