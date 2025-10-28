<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='cidh_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='')   
      $q_get_report="SELECT cidh_code, cidh_date, cidd_id, change_item_description_detail.itemd_id, itemd_code, masti_id_old, masti_id_new,
                                            CONCAT(masti_name,' | ',masti_capacity,' ', uom_name,' | ',cati_name,' | 1 Cylinder') AS masti_name, 
                                           (SELECT CONCAT(masti_name,' | ',masti_capacity,' ', uom_name,' | ',cati_name,' | 1 Cylinder') 
                                            FROM master_item 
											INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
											INNER JOIN uom ON uom.uom_id=master_item.uom_id_1
                                            WHERE masti_id=masti_id_new) AS masti_name_1, 
											itemd_serial_no, cidd_is_canceled, cidd_notes, users_names
                                     FROM change_item_description_detail 
                                     INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id 
                                     INNER JOIN item_detail ON item_detail.itemd_id=change_item_description_detail.itemd_id 
                                     INNER JOIN master_item ON master_item.masti_id=change_item_description_detail.masti_id_old
									 INNER JOIN uom ON uom.uom_id=master_item.uom_id_1
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
									 INNER JOIN users ON users.users_id=change_item_description_header.created_by
									 WHERE change_item_description_header.branch_id='$branch_id' AND $find_text
									 ORDER BY cidh_code, cidh_date DESC, itemd_code, masti_name, cidh_date ASC";
  else
      $q_get_report="SELECT cidh_code, cidh_date, cidd_id, change_item_description_detail.itemd_id, itemd_code, masti_id_old, masti_id_new,
                                            CONCAT(masti_name,' | ',masti_capacity,' ', uom_name,' | ',cati_name,' | 1 Cylinder') AS masti_name, 
                                           (SELECT CONCAT(masti_name,' | ',masti_capacity,' ', uom_name,' | ',cati_name,' | 1 Cylinder') 
                                            FROM master_item 
											INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
											INNER JOIN uom ON uom.uom_id=master_item.uom_id_1
                                            WHERE masti_id=masti_id_new) AS masti_name_1, 
											itemd_serial_no, cidd_is_canceled, cidd_notes, users_names
                                     FROM change_item_description_detail 
                                     INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id 
                                     INNER JOIN item_detail ON item_detail.itemd_id=change_item_description_detail.itemd_id 
                                     INNER JOIN master_item ON master_item.masti_id=change_item_description_detail.masti_id_old
									 INNER JOIN uom ON uom.uom_id=master_item.uom_id_1
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
									 INNER JOIN users ON users.users_id=change_item_description_header.created_by
									 WHERE change_item_description_header.branch_id='$branch_id'
									 ORDER BY cidh_code, cidh_date DESC, itemd_code, masti_name, cidh_date ASC";
  //echo $q_get_report;
  $exec_get_report=mysqli_query($db_connection,$q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Change_Description_Report.xls';
       $header="DATA LAPORAN PERUBAHAN DESKRIPSI ISI ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset Sebelumnya"."\t"."Deskripsi Isi aset Baru"."\t"."Serial no"."\t"."Dibatalkan"."\t"."Dibuat Oleh"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   if ($field_data['cidd_is_canceled']=='0')
			       $is_canceled='Tidak';
               else
			      $is_canceled='Ya';
			   $rows_data=$rows_data.$field_data['cidh_code']."\t".$field_data['cidh_date']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t".$field_data['masti_name_1']."\t"." ".$field_data['itemd_serial_no']."\t".$is_canceled."\t".$field_data['users_names']."\t".$field_data['cidd_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Change_Description_Report.xls");
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