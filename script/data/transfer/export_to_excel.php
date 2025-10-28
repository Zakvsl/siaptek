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
  
  if ($field=='tth_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='') 
      $q_get_data="SELECT tth_id, tth_code, tth_date, branch_id_to, branch_name, tth_ba_no, tth_po_no,
						  CASE tth_status
							   WHEN '0' THEN 'Belum Diterima'
							   WHEN '1' THEN 'Diterima Sebagian'
							   WHEN '2' THEN 'Diterima Semua'
						  END tth_status, 
						  CASE tth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END tth_is_canceled, tth_notes
				   FROM transfer_header
				   INNER JOIN branch ON transfer_header.branch_id_to=branch.branch_id
                   WHERE transfer_header.branch_id='$branch_id' AND $find_text
				   ORDER BY tth_code DESC";
  else
      $q_get_data="SELECT tth_id, tth_code, tth_date, branch_id_to, branch_name, tth_ba_no, tth_po_no,
						  CASE tth_status
							   WHEN '0' THEN 'Belum Diterima'
							   WHEN '1' THEN 'Diterima Sebagian'
							   WHEN '2' THEN 'Diterima Semua'
						  END tth_status,
						  CASE tth_is_canceled
							   WHEN '0' THEN 'Tidak'
							   WHEN '1' THEN 'Ya'
						  END tth_is_canceled,tth_notes
				   FROM transfer_header
				   INNER JOIN branch ON transfer_header.branch_id_to=branch.branch_id
                   WHERE transfer_header.branch_id='$branch_id'
				   ORDER BY tth_code DESC";
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Transfer_Material.xls';
       $header="DAFTAR TRANSAKSI PERPINDAHAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Nomor Berita Acara"."\t"."Purchase No"."\t"."Kantor Cabang Tujuan"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['tth_code']."\t".$field_data['tth_date']."\t".$field_data['tth_ba_no']."\t".$field_data['tth_po_no']."\t".$field_data['branch_name']."\t".$field_data['tth_status']."\t".$field_data['tth_is_canceled']."\t".$field_data['tth_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Transfer_Material.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ditemukan!');
			   window.close();
			</script>
		<?php
	 } 	   
?>