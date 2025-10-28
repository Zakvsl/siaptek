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
  $as_of_date=get_date_2($_POST['txt_aging_date']);
  if (isset($_POST['s_customer']))
      $customer=$_POST['s_customer'];
  else
      $customer=""; 
  $total_cust=count($customer);
  $cust_id='';
  if ($_POST['rb_type_1']=='1')
     { 
	   if ($total_cust==0)
          {
	        ?>
	           <script language="javascript">
		         alert('Tidak ada Customer yang dipilih!');
				 history.back();
		       </script>
	        <?php
			exit;
	      }
		  
	   foreach($customer as $customer_id)
	           {
			     if ($cust_id=='')
			 	     $cust_id="'".$customer_id."'";
		 		 else
				     $cust_id=$cust_id.",'".$customer_id."'";
			   }		  
	 }
  else
	 $cust_id="SELECT cust_id FROM customer";
  /*
     $q_get_data="SELECT cust_id AS cust_id_1, cust_code, cust_name,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date'),0) AS total,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>30 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<61 ),0) AS total_31_60,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>60 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<91 ),0) AS total_61_90,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>90 AND 
								   (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                      IFNULL((SELECT SUM(issuingd_qty) 
                              FROM issuing_detail
                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                              WHERE branch_id='$branch_id' AND issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_status!='2' AND issuingh_is_canceled='0' AND
                                    cust_id=cust_id_1 AND issuingh_date<='$as_of_date' AND (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121
               FROM customer
               WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($cust_id)"; */
	/*$q_get_data="SELECT cust_id AS cust_id_1, cust_code, cust_name,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id`),0) AS total,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
									  (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>30 AND (DATEDIFF('$as_of_date',issuingh_date))<61),0) AS total_31_60,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>60 AND (DATEDIFF('$as_of_date',issuingh_date))<91),0) AS total_61_90,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>90 AND (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 INNER JOIN return_header ON return_header.`issuingh_id`=issuing_header.`issuingh_id`
                                 INNER JOIN return_detail ON return_detail.`reth_id`=return_header.`reth_id`
                                 WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND 
								       issuingd_is_canceled='0' AND issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
                                       cust_id=cust_id_1 AND reth_date<='$as_of_date' AND issuing_detail.issuingd_id!=return_detail.`issuingd_id` AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121
                  FROM customer
                  WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($cust_id)"; */
	$q_get_data="SELECT cust_id AS cust_id_1, cust_code, cust_name,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                        issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`))),0) AS total,
                        IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                        issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                           ) AND
				                       (DATEDIFF('$as_of_date',issuingh_date))<31),0) AS total_1_30,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND
                                      (DATEDIFF('$as_of_date',issuingh_date))>30 AND (DATEDIFF('$as_of_date',issuingh_date))<61),0) AS total_31_60,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>60 AND (DATEDIFF('$as_of_date',issuingh_date))<91),0) AS total_61_90,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>90 AND (DATEDIFF('$as_of_date',issuingh_date))<121 ),0) AS total_91_120,
                         IFNULL((SELECT SUM(issuingd_qty)
                                 FROM issuing_detail 
                                 INNER JOIN issuing_header ON issuing_header.`issuingh_id`=issuing_detail.`issuingh_id`
                                 WHERE  issuing_header.branch_id='$branch_id' AND issuingd_is_canceled='0' AND 
                                        issuingh_date<='$as_of_date' AND issuing_detail.`issuingh_id`=issuing_header.issuingh_id AND
										cust_id=cust_id_1 AND 
                                       issuingd_id NOT IN (SELECT issuingd_id FROM return_detail 
                                                            WHERE reth_id IN (SELECT reth_id FROM return_header 
                                                                              WHERE reth_date<='$as_of_date' AND issuingh_id=issuing_header.`issuingh_id`
                                                                             )
                                                          ) AND 
                                      (DATEDIFF('$as_of_date',issuingh_date))>120),0) AS total_121 
                  FROM customer
                  WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id IN ($cust_id)";
	$exec_get_data=mysqli_query($db_connection, $q_get_data); 
  //echo $customer;
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
      <td scope="col"><h2>SUMMARY AGING REPORT</h2></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td width="52%" scope="col">As Of : <?php echo get_date_1($as_of_date);?></td>
      <td width="46%" align="right"><input type="button" name="btn_back" value="Kembali" onclick="call_back()"/></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="12%" scope="col" class="th_ltb">Kode Customer</th>
          <th width="27%" scope="col" class="th_ltb">Nama Customer</th>
          <th width="13%" scope="col" class="th_ltb">Total</th>
          <th width="9%" scope="col" class="th_ltb">1-30</th>
          <th width="8%" scope="col" class="th_ltb">31-60</th>
          <th width="9%" scope="col" class="th_ltbr">61-90</th>
          <th width="9%" scope="col" class="th_ltbr">91-120</th>
          <th width="9%" scope="col" class="th_ltbr">&gt;120</th>
          </tr>
		  <?php
			$no=0;
		    while ($data=mysqli_fetch_array($exec_get_data))
				  {
					$no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data['cust_id_1'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data['cust_code'];?></td>
          <td class="td_lb"><?php echo $data['cust_name'];?></td>
		  <td class="td_lbr"><?php echo $data['total'];?></td>
          <td class="td_lb"><?php echo $data['total_1_30'];?></td>
          <td class="td_lb"><?php echo $data['total_31_60'];?></td>
          <td class="td_lb"><?php echo $data['total_61_90'];?></td>
          <td class="td_lb"><?php echo $data['total_91_120'];?></td>
          <td class="td_lbr"><?php echo $data['total_121'];?></td>
          </tr><?php } ?>
      </table></td>
        <td width="1%" align="left">&nbsp;</td>
        <td width="1%" align="right">&nbsp;</td>
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
