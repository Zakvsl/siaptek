<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='brokh_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='') 
      $q_get_data="SELECT brokh_id, brokh_code, brokh_date, 
                                   CASE brokh_status
                                        WHEN '0' THEN 'Tidak Dijual'
                                        WHEN '1' THEN 'Dijual Sebagian'
                                        WHEN '2' THEN 'Dijual Semua'
                                   END brokh_status, 
                                   CASE brokh_is_canceled
                                        WHEN '0' THEN 'Tidak'
                                        WHEN '1' THEN 'Ya'
                                   END brokh_is_canceled, brokh_notes
                            FROM broken_header
                            WHERE branch_id='$branch_id' AND $find_text";
  else
      $q_get_data="SELECT brokh_id, brokh_code, brokh_date, 
                                   CASE brokh_status
                                        WHEN '0' THEN 'Tidak Dijual'
                                        WHEN '1' THEN 'Dijual Sebagian'
                                        WHEN '2' THEN 'Dijual Semua'
                                   END brokh_status, 
                                   CASE brokh_is_canceled
                                        WHEN '0' THEN 'Tidak'
                                        WHEN '1' THEN 'Ya'
                                   END brokh_is_canceled, brokh_notes
                            FROM broken_header
                            WHERE branch_id='$branch_id'";
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Broken.xls';
       $header="DAFTAR TRANSAKSI KERUSAKAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['brokh_code']."\t".$field_data['brokh_date']."\t".$field_data['brokh_status']."\t".$field_data['brokh_is_canceled']."\t".$field_data['brokh_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Broken.xls");
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