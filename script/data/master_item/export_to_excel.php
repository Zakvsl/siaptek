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
      $q_get_data="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                                 FROM master_item
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
							     WHERE $find_text";
  else
      $q_get_data="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                                 FROM master_item
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id";
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Master_Item.xls';
       $header="DAFTAR DESKRIPSI ISI ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kode Isi Aset"."\t"."Deskripsi Isi Aset"."\t"."Isi"."\t"."Satuan"."\t"."Kategori"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['masti_code']."\t".$field_data['masti_name']."\t".$field_data['masti_capacity']."\t".$field_data['uom_name_1']."\t".$field_data['cati_name']."\n";
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
			   window.location.href='javascript:history.back(1)';
			</script>
		<?php
	 } 	   
?>