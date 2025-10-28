<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  
  compare_branch($branch_id, $branch_id_transaction);
  
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
      $q_get_data="SELECT rth_id AS rth_id_1, rth_code, tth_code, rth_date, branch_name, 
			              CASE rth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END rth_is_canceled, rth_notes, rth_ba_no, rth_po_no,
						  IF (rth_is_canceled='0',(SELECT COUNT(*) FROM receipt_transfer_detail WHERE rtd_is_canceled='0' AND rth_id=rth_id_1),0) AS total_cylinder  
                   FROM receipt_transfer_header 
                   INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id 
				   INNER JOIN branch ON branch.branch_id=receipt_transfer_header.branch_id_from
                   WHERE receipt_transfer_header.branch_id='$branch_id' AND $find_text
				   ORDER BY tth_code DESC";
  else
      $q_get_data="SELECT rth_id AS rth_id_1, rth_code, tth_code, rth_date, branch_name, 
			              CASE rth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END rth_is_canceled, rth_notes, rth_ba_no, rth_po_no,
						  IF (rth_is_canceled='0',(SELECT COUNT(*) FROM receipt_transfer_detail WHERE rtd_is_canceled='0' AND rth_id=rth_id_1),0) AS total_cylinder  
                   FROM receipt_transfer_header 
                   INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id 
				   INNER JOIN branch ON branch.branch_id=receipt_transfer_header.branch_id_from
                   WHERE receipt_transfer_header.branch_id='$branch_id'
				   ORDER BY tth_code DESC";
  //echo $q_get_data;
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Returning.xls';
       $header="DAFTAR TRANSAKSI PENERIMAAN TRANSFER ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."No Referensi"."\t"."Tanggal"."\t"."Nomor Berita Acara"."\t"."Purchase No"."\t"."Kantor Cabang Asal"."\t"."Total Aset"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['rth_code']."\t".$field_data['tth_code']."\t".$field_data['rth_date']."\t".$field_data['rth_ba_no']."\t".$field_data['rth_po_no']."\t".$field_data['branch_name']."\t".$field_data['total_cylinder']."\t".$field_data['rth_is_canceled']."\t".$field_data['rth_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Receipt_Transfer.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			 //  window.close();
			</script>
		<?php
	 } 	   
?>