<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='reth_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='') 
      $q_get_data="SELECT reth_id AS reth_id_1, reth_code, issuingh_code, reth_date, reth_ref_no, cust_name, whsl_name, 
	                      CASE reth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END reth_is_canceled, reth_notes, reth_ba_no, reth_po_no, 
						  IF (reth_is_canceled='0',(SELECT COUNT(*) FROM return_detail WHERE retd_is_canceled='0' AND reth_id=reth_id_1),0) AS total_cylinder 
                            FROM return_header 
                            INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id 
                            INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
							INNER JOIN warehouse_location ON warehouse_location.whsl_id=return_header.whsl_id
                            WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_text
							ORDER BY reth_code DESC";
  else
      $q_get_data="SELECT reth_id AS reth_id_1, reth_code, issuingh_code, reth_date, reth_ref_no, cust_name, whsl_name, 
	                      CASE reth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END reth_is_canceled, reth_notes,reth_ba_no, reth_po_no, 
						  IF (reth_is_canceled='0',(SELECT COUNT(*) FROM return_detail WHERE retd_is_canceled='0' AND reth_id=reth_id_1),0) AS total_cylinder 
                            FROM return_header 
                            INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id 
                            INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
							INNER JOIN warehouse_location ON warehouse_location.whsl_id=return_header.whsl_id
                            WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' 
							ORDER BY reth_code DESC";
 // echo $q_get_data;
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Returning.xls';
       $header="DAFTAR TRANSAKSI PENGEMBALIAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."No Ref Pengeluaran"."\t"."Tanggal"."\t"."Ref No"."\t"."Nomor Berita Acara"."\t"."Purchase No"."\t"."Nama Customer/Vendor"."\t"."Total Aset"."\t"."Lokasi Gudang"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['reth_code']."\t".$field_data['issuingh_code']."\t".$field_data['reth_date']."\t".$field_data['reth_ref_no']."\t".$field_data['reth_ba_no']."\t".$field_data['reth_po_no']."\t".$field_data['cust_name']."\t".$field_data['total_cylinder']."\t".$field_data['whsl_name']."\t".$field_data['reth_is_canceled']."\t".$field_data['reth_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Returning.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			   window.close();
			</script>
		<?php
	 } 	   
?>