<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find=htmlspecialchars($_GET['t']);
  $select_all_branch=htmlspecialchars($_GET['b']);
  
  if ($select_all_branch=='1')
	  $all_branch="";
  else
	  $all_branch="branch_id='$branch_id' AND ";
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find."'";	
  
  if ($text_to_find!='')  
      $q_get_data="SELECT masti_id AS masti_id_1, masti_code, masti_name, masti_capacity, uom_id_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1,
                          uom_id_2, cati_name,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1) AS qty_total,
                         (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,
                         ((SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1)-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_broken='1')-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_wo='1')-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_dispossed='1')) AS qty_active,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_broken='1') AS qty_broken,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_wo='1') AS qty_wo,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_dispossed='1') AS qty_dispossed
                   FROM master_item
                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
				   WHERE $find_text";
  else
      $q_get_data="SELECT masti_id AS masti_id_1, masti_code, masti_name, masti_capacity, uom_id_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1,
                          uom_id_2, cati_name,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1) AS qty_total,
                         (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,
                         ((SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1)-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_broken='1')-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_wo='1')-
                          (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_dispossed='1')) AS qty_active,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_broken='1') AS qty_broken,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_wo='1') AS qty_wo,
                         (SELECT COUNT(*) FROM item_detail WHERE $all_branch masti_id=masti_id_1 AND itemd_is_dispossed='1') AS qty_dispossed
                   FROM master_item
                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id";
  //echo $q_get_data;
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $no=1;
	   $filename='Summary_Item.xls';
	   if ($select_all_branch=='1')
           $header="DAFTAR SUMMARY ASET SEMUA CABANG"."\n";
	   else
	       $header="DAFTAR SUMMARY ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No"."\t"."Kode Isi Aset"."\t"."Deskripsi Isi Aset"."\t"."Isi"."\t"."Kategori"."\t"."Qty Total"."\t"."Qty Aktif"."\t"."Qty Rusak"."\t"."Qty Dihapus"."\t"."Qty Dijual"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$no++."\t".$field_data['masti_code']."\t".$field_data['masti_name']."\t".$field_data['masti_capacity']." ".$field_data['uom_name_1']."\t".$field_data['cati_name']."\t".$field_data['qty_total']." ".$field_data['uom_name_2']."\t".$field_data['qty_active']."\t".$field_data['qty_broken']."\t".$field_data['qty_wo']."\t".$field_data['qty_dispossed']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Mater_Item.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			   history.back(1);
			</script>
		<?php
	 } 	   
?>