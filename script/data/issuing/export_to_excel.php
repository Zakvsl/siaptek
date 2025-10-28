<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='issuingh_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='')  
      $q_get_data="SELECT issuingh_id AS issuingh_id_1, issuingh_code, issuingh_date, issuingh_do_no, issuingh_ba_no, issuingh_po_no,
                             CASE issuingh_type
                                  WHEN '0' THEN 'Customer'
                                  WHEN '1' THEN 'Vendor'
                             END issuingh_type,
							 CASE issuingh_status
                                  WHEN '0' THEN 'Belum Kembali'
                                  WHEN '1' THEN 'Kembali Sebagian'
								  WHEN '2' THEN 'Kembali Semua'
                             END issuingh_status,
							 CASE issuingh_is_canceled
							      WHEN '0' THEN 'Tidak'
								  WHEN '1' THEN 'Ya'
							 END issuingh_is_canceled, cust_name, issuingh_notes,
							 IF (issuingh_is_canceled='0',(SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_canceled='0' AND issuingh_id=issuingh_id_1),0) AS total_cylinder 
                             FROM issuing_header
							 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                             WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_text
							 ORDER BY issuingh_code DESC";
  else
      $q_get_data="SELECT issuingh_id AS issuingh_id_1, issuingh_code, issuingh_date, issuingh_do_no, issuingh_ba_no, issuingh_po_no,
                             CASE issuingh_type
                                  WHEN '0' THEN 'Customer'
                                  WHEN '1' THEN 'Vendor'
                             END issuingh_type,
							 CASE issuingh_status
                                  WHEN '0' THEN 'Belum Kembali'
                                  WHEN '1' THEN 'Kembali Sebagian'
								  WHEN '2' THEN 'Kembali Semua'
                             END issuingh_status,
							 CASE issuingh_is_canceled
							      WHEN '0' THEN 'Tidak'
								  WHEN '1' THEN 'Ya'
							 END issuingh_is_canceled, cust_name, issuingh_notes,
							 IF (issuingh_is_canceled='0',(SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_canceled='0' AND issuingh_id=issuingh_id_1),0) AS total_cylinder 
                             FROM issuing_header
							 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                             WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id'
							 ORDER BY issuingh_code DESC";
  $exec_get_data=mysqli_query($db_connection,$q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Issuing.xls';
       $header="DAFTAR TRANSAKSI PENGELUARAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Nomor Berita Acara"."\t"."Purchase No"."\t"."DO No"."\t"."Tipe"."\t"."Nama Customer/Vendor"."\t"."Status"."\t"."Total Aset"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['issuingh_ba_no']."\t".$field_data['issuingh_po_no']."\t".$field_data['issuingh_do_no']."\t".$field_data['issuingh_type']."\t".$field_data['cust_name']."\t".$field_data['issuingh_status']."\t".$field_data['total_cylinder']."\t".$field_data['issuingh_is_canceled']."\t".$field_data['issuingh_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Issuing.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			   window.location.href='javascript:history.back(1)';
			</script>
		<?php
	 } 	   
?>