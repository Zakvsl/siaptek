<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  $type_1=$_POST['rb_type_1'];
  $start_date=get_date_2($_POST['txt_start_date']);
  $end_date=get_date_2($_POST['txt_end_date']);
  if (isset($_POST['s_item_1']))
      $item=$_POST['s_item_1'];
  else
      $item="";
  $item_list='';
  if ($type_1=='1')
     {
       foreach ($item as $tube)
               {
		        if ($item_list=='')
		            $item_list="'".$tube."'";
		        else
		            $item_list=$item_list.",'".$tube."'";
			   }
     } 
  else
     {
	   $item_list="SELECT item_detail.itemd_id 
                   FROM item_detail 
                   WHERE branch_id='$branch_id' ";
	 }
	 
  $q_get_data="SELECT issuingh_code AS transaction_code, issuingh_date AS transaction_date, 'Pengeluaran Aset' AS transaction_type, 
                      CASE issuingd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
               INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
               WHERE issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND issuingh_date BETWEEN '$start_date' AND '$end_date' AND 
			         item_detail.itemd_id IN ($item_list)
               UNION
               SELECT reth_code AS transaction_code, reth_date AS transaction_date, 'Pengembalian Aset' AS transaction_type, 
                      CASE retd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
               INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
               INNER JOIN return_detail ON return_detail.issuingd_id=issuing_detail.issuingd_id
               INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
               WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND 
			         reth_date BETWEEN '$start_date' AND '$end_date' AND item_detail.itemd_id IN ($item_list)
               UNION
               SELECT tth_code AS transaction_code, tth_date AS transaction_date, 'Perpindahan Aset' AS transaction_type, 
                      CASE ttd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id
               INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
               WHERE transfer_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND tth_date BETWEEN '$start_date' AND '$end_date'  AND 
			         item_detail.itemd_id IN ($item_list)
               UNION
               SELECT disph_code AS transaction_code, disph_date AS transaction_date, 'Penjualan Aset' AS transaction_type, 
                      CASE dispd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN dispossal_detail ON dispossal_detail.itemd_id=item_detail.itemd_id
               INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
               WHERE dispossal_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND disph_date BETWEEN '$start_date' AND '$end_date'  AND 
			         item_detail.itemd_id IN ($item_list)
               ORDER BY itemd_code, transaction_date, transaction_type ASC";
	$exec_get_data=mysqli_query($db_connection,$q_get_data); 
 //echo $q_get_data;
?>
<style>
            #action {
                background-color : black;
                width : 120px;
                height : 28px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul {
                padding:0;
                margin:0;
                list-style-type:none;
            }
             
            #action ul li {
                float:left;
                position : relative;
            }
             
            #action ul li a {
                display:block;
                padding:5px 10px;
                color:#fff;
                text-decoration:none;
                font-family: calibri;
				font-size:14px;
            }
             
            #action ul li a:hover {
                background-color:#72b626;
            }
             
            /* Menu Dropdown */
            
            #action ul li ul {
                display: none;
            }
             
            #action ul li:hover ul {
                display:block;
                position: absolute;
            }
            
            #action ul li:hover ul li a {
                display:block;
                background-color : black;
                color : #fff;
                width : 100px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul li:hover ul li a:hover {
                background-color : #72b626;
            }
            
            #action ul li:hover > a {
			    background: #72b626;
	  	    }
        </style>
		
<form id="f_customer" name="f_customer" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td scope="col"><h2>LAPORAN PERGERAKAN ASET</h2></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td width="58%" scope="col">Periode  : <?php echo get_date_1($start_date)." Sampai Dengan ".get_date_1($end_date);?></td>
      <td width="42%" align="right"><input type="button" name="btn_back" value="Kembali" onclick="call_back()"/></td>
    </tr>
    <tr>
      <td colspan="2">
	   <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="3%" scope="col" class="th_ltb">No</th>
          <th width="8%" scope="col" class="th_ltb">Kode Aset </th>
          <th width="21%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="7%" scope="col" class="th_ltbr">Kapasitas</th>
          <th width="4%" scope="col" class="th_ltbr">Qty</th>
          <th width="7%" scope="col" class="th_ltbr">Serial No</th>
          <th width="8%" scope="col" class="th_ltbr">Tgl Perolehan</th>
          <th width="7%" scope="col" class="th_ltbr">Kategori</th>
		  <th width="11%" scope="col" class="th_ltb">No Transaksi</th>
          <th width="7%" scope="col" class="th_ltb">Tanggal</th>
          <th width="10%" scope="col" class="th_ltb">Jenis Transaksi</th>
          <th width="7%" scope="col" class="th_ltb">Dibatalkan</th>
        </tr>
		  <?php 
		        $no=1;
				if (mysqli_num_rows($exec_get_data)>0)
				   {
		             while ($field_data=mysqli_fetch_array($exec_get_data))
		                   {
					         echo "<tr>";
						     echo "<td>".$no++."</td>";
		                     echo "<td>".$field_data['itemd_code']."</td>";
		                     echo "<td>".$field_data['masti_name']."</td>";
		                     echo "<td>".$field_data['itemd_capacity']." ".$field_data['uom_name']."</td>";
		                     echo "<td>".$field_data['itemd_qty']." Cylinder"."</td>";
		                     echo "<td>".$field_data['itemd_serial_no']."</td>";
		                     echo "<td>".get_date_1($field_data['itemd_acquired_date'])."</td>";
		                     echo "<td>".$field_data['cati_name']."</td>";
							 echo "<td>".$field_data['transaction_code']."</td>";
		                     echo "<td>".get_date_1($field_data['transaction_date'])."</td>";
		                     echo "<td>".$field_data['transaction_type']."</td>";
		                     echo "<td>".$field_data['is_canceled']."</td>";
		                     echo "</tr>";
					       }
				   }
		 ?>
       </table><p align="center">
	  </td>
    </tr>
  </table>
</form>

<script language="javascript">
   function call_back()
            {
			  history.back();
			}
</script>

</body>
</html>
