<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  include "../../library/style.css";
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  $q_check_user="SELECT * FROM users WHERE users_id='$id'";
  $exec_check_user=mysqli_query($db_connection, $q_check_user);
  $field_user=mysqli_fetch_array($exec_check_user);
  if (mysqli_num_rows($exec_check_user)==0)
     {
	   ?>
          <script language="javascript"> 
		    alert('User tidak ditemukan!');
		    window.close();
	      </script>
	   <?php 
	 }
  else
  if ($field_user['users_status']=='1')
     {
	   ?>
          <script language="javascript"> 
		    alert('Status User InActive!');
		    window.close();
	      </script>
	   <?php
	 }
  else
  if ($field_user['users_level']=='0')
     {
	   ?>
          <script language="javascript"> 
		    alert('User level adalah Super Administrator!');
		    window.close();
	      </script>
	   <?php
	 }
  else
  if ($field_user['users_level']=='1' || $field_user['users_level']=='2')
     {
       if (isset($_GET['b']))
          {
	        $branch_id=mysqli_real_escape_string($db_connection, $_GET['b']);
	        $q_check_users_authorization="SELECT * FROM users_authorization WHERE users_id='$id' AND branch_id='$branch_id'";
            $exec_check_users_authorization=mysqli_query($db_connection, $q_check_users_authorization);
		//	echo $q_check_users_authorization;
			if (mysqli_num_rows($exec_check_users_authorization)>0)
			   {
                 while ($field_data=mysqli_fetch_array($exec_check_users_authorization))
                       {
					     if ($field_data['menu']=='BRANCH')
					          $branch_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='WHS')
					          $whs_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ITEM_CATEGORY')
					          $ctg_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='MASTER_ITEM')
					         $masti_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='ITEM_DETAIL')
					          $itemd_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='ITEM_DETAIL_ALL')
					          $itemd_all_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='SUMMARY_ITEM')
					          $summary_item_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='TRANSFER_ITEM')
					          $transfer_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='RECEIPT_TRANSFER_ITEM')
					          $receipt_transfer_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ISSUING')
					          $issuing_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='RETURNING')
					          $returning_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='BROKEN')
					          $broken_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='WRITE_OFF')
					          $write_off_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='DISPOSSAL')
					          $dispossal_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CHANGE_DESCRIPTION')
					          $change_description_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ISSUING_REPORT')
					          $issuing_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='RETURNING_REPORT')
					          $returning_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='BROKEN_REPORT')
					          $broken_report_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='WRITE_OFF_REPORT')
					          $write_off_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='DISPOSSAL_REPORT')
					          $dispossal_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CHANGE_DESCRIPTION_REPORT') 
					          $cid_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='POSITION_REPORT')
					          $position_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='AGING_REPORT')
					          $aging_report_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='SOA_REPORT')
					          $soa_report_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='VENDOR_ITEM_REPORT')
					          $vendor_item_report_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='HISTORY_MOVEMENT_REPORT')
					          $history_movement_report_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='TRANSFER_REPORT')
					          $transfer_report_autho=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='DOC_FLOW_REPORT')
						      $doc_flow_report_autho=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='EMPLOYEE')
					          $employee_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='UOM')
					          $uom_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CUSTOMER_TYPE')
					          $customer_type_autho=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='CUSTOMER')
					          $customer_autho=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='VENDOR')
					          $vendor_autho=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='APT_PERIOD')
					          $apt_period_autho=$field_data['authorization'];
			           }
			   }
			else
			   {
			     $branch_autho='N';
		         $whs_autho='N';
		         $ctg_autho='N';
		         $masti_autho='N';
		         $itemd_autho='N';
				 $itemd_all_autho='N';
				 $summary_item_autho='N';
		         $transfer_autho='N';
				 $receipt_transfer_autho='N';
		         $issuing_autho='N';
		         $returning_autho='N';
		         $broken_autho='N';
				 $write_off_autho='N';
		         $dispossal_autho='N';
		         $change_description_autho='N';
		         $issuing_report_autho='N';
		         $returning_report_autho='N';
		         $broken_report_autho='N';
				 $write_off_report_autho='N';
		         $dispossal_report_autho='N';
		         $cid_report_autho='N';
		         $position_report_autho='N';
		         $aging_report_autho='N';
				 $soa_report_autho='N';
		         $vendor_item_report_autho='N';
		         $history_movement_report_autho='N';
				 $transfer_report_autho='N';
				 $doc_flow_report_autho='N';
		         $employee_autho='N';
		         $uom_autho='N';
				 $customer_type_autho='N';
		         $customer_autho='N';
		         $vendor_autho='N';
				 $apt_period_autho='N';
			   } 
	      }
	   else
	      {
            $branch_autho='N';
		    $whs_autho='N';
		    $ctg_autho='N';
		    $masti_autho='N';
		    $itemd_autho='N';
			$itemd_all_autho='N';
			$summary_item_autho='N';
		    $transfer_autho='N';
			$receipt_transfer_autho='N';
		    $issuing_autho='N';
		    $returning_autho='N';
		    $broken_autho='N';
			$write_off_autho='N';
		    $dispossal_autho='N';
		    $change_description_autho='N';
		    $issuing_report_autho='N';
		    $returning_report_autho='N';
		    $broken_report_autho='N';
			$write_off_report_autho='N';
		    $dispossal_report_autho='N';
		    $cid_report_autho='N';
		    $position_report_autho='N';
		    $aging_report_autho='N';
			$soa_report_autho='N';
		    $vendor_item_report_autho='N';
		    $history_movement_report_autho='N';
			$transfer_report_autho='N';
			$doc_flow_report_autho='N';
		    $employee_autho='N';
		    $uom_autho='N';
			$customer_type_autho='N';
		    $customer_autho='N';
		    $vendor_autho='N'; 
			$apt_period_autho='N';       
		  }
  }
?>
<form name="form1" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-list">
  <tr>
    <td colspan="6" scope="col" align="center"><h2>UPDATE USER AUTHORIZATION</h2></td>
  </tr>
  <tr>
    <td width="17%">&nbsp;</td>
    <td width="6%">&nbsp;</td>
    <td width="26%">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6">
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr bgcolor="#CCCCCC">
        <th width="2%" scope="col"><input type="checkbox" name="cb_all" id="cb_all" value="" onclick="select_unselect_all()"/></th>
        <th width="15%" scope="col">Kode Kantor Cabang</th>
        <th width="67%" scope="col">Nama Kantor Cabang</th>
        <th width="16%" scope="col">Action</th>
      </tr>
	       <?php
	          $q_check_user_branch="SELECT branch.branch_id, branch_code, branch_name
                                    FROM branch
                                    INNER JOIN users_branch ON branch.branch_id=users_branch.branch_id
                                    INNER JOIN users ON users.users_id=users_branch.users_id
                                    WHERE users_branch.users_id='$id'";
	          $exec_check_user_branch=mysqli_query($db_connection, $q_check_user_branch);
	          if (mysqli_num_rows($exec_check_user_branch)>0)
			     { 
				   while ($field_data=mysqli_fetch_array($exec_check_user_branch))
				         {
                           echo "<tr>";
                           echo "<td><input type='checkbox' id='cb_data[]' name='cb_data[]' value='".$field_data['branch_id']."' /></td>";
                           echo "<td>".$field_data['branch_code']."</td>";
                           echo "<td>".$field_data['branch_name']."</td>";
                           echo  "<td align='center'><input type='button' name='btn_display_authorization' value='Tampilkan Autorisasi' 
						                                    onclick='display_authorization(".$field_data['branch_id'].")'/></td>";
                           echo "</tr>";
					     }		 
				 }
			  else
			     {
				   echo "<tr>";
				   echo "<td colspan='4' align='center'>Tidak Ada Authorisasi Kantor Cabang</td>";  
				   echo "</tr>";
				 }	  
	     ?>
    </table></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">Tipe Settingan User: 
      <input name="rb_setting_type" type="radio" value="0" checked="checked"/>
      Satu Per Satu 
      <input name="rb_setting_type" type="radio" value="1" />
      Semua</td>
    <td width="19%">&nbsp;</td>
    <td width="0">&nbsp;</td>
    <td width="32%" align="right"><input type="button" name="btn_authorization" value="Autorisasi Kantor Cabang" onclick="call_setting_branch('<?php echo $id;?>')"/>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onclick="window.close()"/></td>
  </tr>
  <tr bgcolor="#FF0000">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" valign="top">
	  <table border="0" width="100%" cellspacing="0" cellpadding="0" >
	         <tr>
			      <th colspan="3">PERUSAHAAN</th>
			 </tr>
			 <tr>
			      <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kantor Cabang</td>
				  <td width="2%">:</td>
				  <td width="55%"><?php
		                                if ($branch_autho=='N')
			                               {
			                                 $branch_n="checked='checked'";
				                             $branch_r="";
				                             $branch_w="";
				                             $branch_d="";
			                               }
			                            else
			                            if ($branch_autho=='R')
			                               {
			                                 $branch_n="";
				                             $branch_r="checked='checked'";
				                             $branch_w="";
				                             $branch_d="";
			                               }
			                            else
			                            if ($branch_autho=='W')
			                               {
			                                 $branch_n="";
				                             $branch_r="";
				                             $branch_w="checked='checked'";
				                             $branch_d="";
			                               }
			                            else
			                            if ($branch_autho=='D')
			                               {
			                                 $branch_n="";
				                             $branch_r="";
				                             $branch_w="";
				                             $branch_d="checked='checked'";
			                               }   
		                              ?>
	                                <input name="rb_branch" id="rb_branch" type="radio" value="N" <?php echo $branch_n; ?>/>None&nbsp;
                                    <input name="rb_branch" id="rb_branch" type="radio" value="R" <?php echo $branch_r; ?>/>Read&nbsp;
                                    <input name="rb_branch" id="rb_branch" type="radio" value="W" <?php echo $branch_w; ?>/>Write&nbsp;
                                    <input name="rb_branch" id="rb_branch" type="radio" value="D" <?php echo $branch_d; ?>/>Delete</td>
			 </tr>
			 <tr>
			      <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lokasi Gudang </td>
				  <td width="2%">:</td>
				  <td width="55%"><?php
		                               if ($whs_autho=='N')
			                              {
			                                $whs_n="checked='checked'";
				                            $whs_r="";
				                            $whs_w="";
				                            $whs_d="";
			                              }
			                           else
			                           if ($whs_autho=='R')
			                              {
			                                $whs_n="";
				                            $whs_r="checked='checked'";
				                            $whs_w="";
				                            $whs_d="";
			                              }
			                           else
			                           if ($whs_autho=='W')
			                              {
			                                $whs_n="";
				                            $whs_r="";
				                            $whs_w="checked='checked'";
				                            $whs_d="";
			                              }
			                           else
			                           if ($whs_autho=='D')
			                              {
			                                $whs_n="";
				                            $whs_r="";
				                            $whs_w="";
				                            $whs_d="checked='checked'";
			                              }   
		                             ?>
		                           <input name="rb_whs" id="rb_whs" type="radio" value="N" <?php echo $whs_n; ?>/>None&nbsp;
                                   <input name="rb_whs" id="rb_whs" type="radio" value="R" <?php echo $whs_r; ?>/>Read&nbsp;
                                   <input name="rb_whs" id="rb_whs" type="radio" value="W" <?php echo $whs_w; ?>/>Write&nbsp;
                                   <input name="rb_whs" id="rb_whs" type="radio" value="D" <?php echo $whs_d; ?>/>Delete				  
				  </td>
			 </tr>
	  </table><br />
	  <table border="0" width="100%" cellspacing="0" cellpadding="0" >
	         <tr>
			      <th colspan="3">ASET</th>
			 </tr>
			 <tr>
			      <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kategori Aset</td>
				  <td width="2%">:</td>
				  <td width="55%"><?php
		                                if ($ctg_autho=='N')
			                               {
			                                 $ctg_n="checked='checked'";
				                             $ctg_r="";
				                             $ctg_w="";
				                             $ctg_d="";
			                               }
			                            else
			                            if ($ctg_autho=='R')
			                               {
			                                 $ctg_n="";
				                             $ctg_r="checked='checked'";
				                             $ctg_w="";
				                             $ctg_d="";
			                               }
			                            else
			                            if ($ctg_autho=='W')
			                               {
			                                 $ctg_n="";
				                             $ctg_r="";
				                             $ctg_w="checked='checked'";
				                             $ctg_d="";
			                               }
			                            else
			                            if ($ctg_autho=='D')
			                               {
			                                 $ctg_n="";
				                             $ctg_r="";
				                             $ctg_w="";
				                             $ctg_d="checked='checked'";
			                               }   
		                              ?>
                                      <input name="rb_ctg" id="rb_ctg" type="radio" value="N" <?php echo $ctg_n; ?>/>None&nbsp;
                                      <input name="rb_ctg" id="rb_ctg" type="radio" value="R" <?php echo $ctg_r; ?>/>Read&nbsp;
                                      <input name="rb_ctg" id="rb_ctg" type="radio" value="W" <?php echo $ctg_w; ?>/>Write&nbsp;
                                      <input name="rb_ctg" id="rb_ctg" type="radio" value="D" <?php echo $ctg_d; ?>/>Delete				  </td>
			 </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi Isi Aset </td>
			   <td>:</td>
			   <td><?php
		                if ($masti_autho=='N')
			               {
			                 $masti_n="checked='checked'";
				             $masti_r="";
				             $masti_w="";
				             $masti_d="";
			               }
			            else
			            if ($masti_autho=='R')
			               {
			                 $masti_n="";
				             $masti_r="checked='checked'";
				             $masti_w="";
				             $masti_d="";
			               }
			            else
			            if ($masti_autho=='W')
			               {
			                 $masti_n="";
				             $masti_r="";
				             $masti_w="checked='checked'";
				             $masti_d="";
			               }
			            else
			            if ($masti_autho=='D')
			               {
			                 $masti_n="";
				             $masti_r="";
				             $masti_w="";
				             $masti_d="checked='checked'";
			               }   
		              ?>
                      <input name="rb_masti" id="rb_masti" type="radio" value="N" <?php echo $masti_n; ?>/>None&nbsp;
                      <input name="rb_masti" id="rb_masti" type="radio" value="R" <?php echo $masti_r; ?>/>Read&nbsp;
                      <input name="rb_masti" id="rb_masti" type="radio" value="W" <?php echo $masti_w; ?>/>Write&nbsp;
                      <input name="rb_masti" id="rb_clasm" type="radio" value="D" <?php echo $masti_d; ?>/>Delete		       </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aset</td>
			   <td>:</td>
			   <td><?php
		                               if ($itemd_autho=='N')
			                              {
			                                $itemd_n="checked='checked'";
				                            $itemd_r="";
				                            $itemd_w="";
				                            $itemd_d="";
			                              }
			                           else
			                           if ($itemd_autho=='R')
			                              {
			                                $itemd_n="";
				                            $itemd_r="checked='checked'";
				                            $itemd_w="";
				                            $itemd_d="";
			                              }
			                           else
			                           if ($itemd_autho=='W')
			                              {
			                                $itemd_n="";
				                            $itemd_r="";
				                            $itemd_w="checked='checked'";
				                            $itemd_d="";
			                              }
			                           else
			                           if ($itemd_autho=='D')
			                              {
			                                $itemd_n="";
				                            $itemd_r="";
				                            $itemd_w="";
				                            $itemd_d="checked='checked'";
			                              }   
		                             ?>
                 <input name="rb_itemd" id="radio2" type="radio" value="N" <?php echo $itemd_n; ?>/>None&nbsp;
                 <input name="rb_itemd" id="radio2" type="radio" value="R" <?php echo $itemd_r; ?>/>Read&nbsp;
                 <input name="rb_itemd" id="radio2" type="radio" value="W" <?php echo $itemd_w; ?>/>Write&nbsp;
                 <input name="rb_itemd" id="radio2" type="radio" value="D" <?php echo $itemd_d; ?>/>Delete </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aset Semua Cabang</td>
			   <td>:</td>
			   <td><?php
		                              if ($itemd_all_autho=='N')
			                              {
			                                $itemd_all_n="checked='checked'";
				                            $itemd_all_r="";
				                            $itemd_all_w="";
				                            $itemd_all_d="";
			                              }
			                           else
			                           if ($itemd_all_autho=='R')
			                              {
			                                $itemd_all_n="";
				                            $itemd_all_r="checked='checked'";
				                            $itemd_all_w="";
				                            $itemd_all_d="";
			                              }
			                           else
			                           if ($itemd_all_autho=='W')
			                              {
			                                $itemd_all_n="";
				                            $itemd_all_r="";
				                            $itemd_all_w="checked='checked'";
				                            $itemd_all_d="";
			                              }
			                           else
			                           if ($itemd_all_autho=='D')
			                              {
			                                $itemd_all_n="";
				                            $itemd_all_r="";
				                            $itemd_all_w="";
				                            $itemd_all_d="checked='checked'";
			                              }   
		                             ?>
                 <input name="rb_itemd_all" id="rb_itemd_all" type="radio" value="N" <?php echo $itemd_all_n; ?>/>None&nbsp;
                 <input name="rb_itemd_all" id="rb_itemd_all" type="radio" value="R" <?php echo $itemd_all_r; ?>/>Read&nbsp;
                 <input name="rb_itemd_all" id="rb_itemd_all" type="radio" value="W" <?php echo $itemd_all_w; ?>/>Write&nbsp;
                 <input name="rb_itemd_all" id="rb_itemd_all" type="radio" value="D" <?php echo $itemd_all_d; ?>/>Delete</td>
	         </tr> 
			 <tr>
			      <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Summary Aset</td>
				  <td width="2%">:</td>
				  <td width="55%"><?php
		                               if ($summary_item_autho=='N')
			                              {
			                                $summary_item_n="checked='checked'";
				                            $summary_item_r="";
				                            $summary_item_w="";
				                            $summary_item_d="";
			                              }
			                           else
			                           if ($summary_item_autho=='R')
			                              {
			                                $summary_item_n="";
				                            $summary_item_r="checked='checked'";
				                            $summary_item_w="";
				                            $summary_item_d="";
			                              }
			                           else
			                           if ($summary_item_autho=='W')
			                              {
			                                $summary_item_n="";
				                            $summary_item_r="";
				                            $summary_item_w="checked='checked'";
				                            $summary_item_d="";
			                              }
			                           else
			                           if ($summary_item_autho=='D')
			                              {
			                                $summary_item_n="";
				                            $summary_item_r="";
				                            $summary_item_w="";
				                            $summary_item_d="checked='checked'";
			                              }   
		                             ?>
                                     <input name="rb_summary_item" id="rb_summary_item" type="radio" value="N" <?php echo $summary_item_n; ?>/>None&nbsp;
                                     <input name="rb_summary_item" id="rb_summary_item" type="radio" value="R" <?php echo $summary_item_r; ?>/>Read&nbsp;
                                     <input name="rb_summary_item" id="rb_summary_item" type="radio" value="W" <?php echo $summary_item_w; ?>/>Write&nbsp;
                                     <input name="rb_summary_item" id="rb_summary_item" type="radio" value="D" <?php echo $summary_item_d; ?>/>Delete				  </td>
			 </tr>
	  </table>
	  <br />
	  <table border="0" width="100%" cellspacing="0" cellpadding="0" >
	         <tr>
			      <th colspan="3">TRANSAKSI</th>
			 </tr>
			 <tr>
			      <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transfer Posisi Aset</td>
				  <td width="2%">:</td>
				  <td width="55%"><?php
		                               if ($transfer_autho=='N')
			                              {
			                                $transfer_n="checked='checked'";
				                            $transfer_r="";
				                            $transfer_w="";
				                            $transfer_d="";
			                              }
			                           else
			                           if ($transfer_autho=='R')
			                              {
			                                $transfer_n="";
				                            $transfer_r="checked='checked'";
				                            $transfer_w="";
				                            $transfer_d="";
			                              }
			                           else
			                           if ($transfer_autho=='W')
			                              {
			                                $transfer_n="";
				                            $transfer_r="";
				                            $transfer_w="checked='checked'";
				                            $transfer_d="";
			                              }
			                           else
			                           if ($transfer_autho=='D')
			                              {
			                                $transfer_n="";
				                            $transfer_r="";
				                            $transfer_w="";
				                            $transfer_d="checked='checked'";
			                              }   
		                             ?>
                                     <input name="rb_transfer" id="rb_transfer" type="radio" value="N" <?php echo $transfer_n; ?>/>None&nbsp;
                                     <input name="rb_transfer" id="rb_transfer" type="radio" value="R" <?php echo $transfer_r; ?>/>Read&nbsp;
                                     <input name="rb_transfer" id="rb_transfer" type="radio" value="W" <?php echo $transfer_w; ?>/>Write&nbsp;
                                     <input name="rb_transfer" id="rb_transfer" type="radio" value="D" <?php echo $transfer_d; ?>/>Delete				    </td>
			 </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penerimaan Transfer Posisi Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($receipt_transfer_autho=='N')
			               {
			                 $receipt_transfer_n="checked='checked'";
				             $receipt_transfer_r="";
				             $receipt_transfer_w="";
				             $receipt_transfer_d="";
			               }
			            else
			            if ($receipt_transfer_autho=='R')
			               {
			                 $receipt_transfer_n="";
				             $receipt_transfer_r="checked='checked'";
				             $receipt_transfer_w="";
				             $receipt_transfer_d="";
			               }
			            else
			            if ($receipt_transfer_autho=='W')
			               {
			                 $receipt_transfer_n="";
				             $receipt_transfer_r="";
				             $receipt_transfer_w="checked='checked'";
				             $receipt_transfer_d="";
			               }
			            else
			            if ($receipt_transfer_autho=='D')
			               {
			                 $receipt_transfer_n="";
				             $receipt_transfer_r="";
				             $receipt_transfer_w="";
				             $receipt_transfer_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_receipt_transfer" id="rb_receipt_transfer" type="radio" value="N" <?php echo $receipt_transfer_n; ?>/>None&nbsp;
                   <input name="rb_receipt_transfer" id="rb_receipt_transfer" type="radio" value="R" <?php echo $receipt_transfer_r; ?>/>Read&nbsp;
                   <input name="rb_receipt_transfer" id="rb_receipt_transfer" type="radio" value="W" <?php echo $receipt_transfer_w; ?>/>Write&nbsp;
                   <input name="rb_receipt_transfer" id="rb_receipt_transfer" type="radio" value="D" <?php echo $receipt_transfer_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pengeluaran Aset</td>
			   <td>:</td>
			   <td><?php
		                 if ($issuing_autho=='N')
			                {
			                  $issuing_n="checked='checked'";
				              $issuing_r="";
				              $issuing_w="";
				              $issuing_d="";
			                }
			             else
			             if ($issuing_autho=='R')
			                {
			                  $issuing_n="";
				              $issuing_r="checked='checked'";
				              $issuing_w="";
				              $issuing_d="";
			                }
			             else
			             if ($issuing_autho=='W')
			                {
			                  $issuing_n="";
				              $issuing_r="";
				              $issuing_w="checked='checked'";
				              $issuing_d="";
			                }
			             else
			             if ($issuing_autho=='D')
			                {
			                  $issuing_n="";
				              $issuing_r="";
				              $issuing_w="";
				              $issuing_d="checked='checked'";
			                }   
		           ?>
                   <input name="rb_issuing" id="rb_issuing" type="radio" value="N" <?php echo $issuing_n; ?>/>None&nbsp;
                   <input name="rb_issuing" id="rb_issuing" type="radio" value="R" <?php echo $issuing_r; ?>/>Read&nbsp;
                   <input name="rb_issuing" id="rb_issuing" type="radio" value="W" <?php echo $issuing_w; ?>/>Write&nbsp;
                   <input name="rb_issuing" id="rb_issuing" type="radio" value="D" <?php echo $issuing_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Pengembalian Aset </td>
			   <td>:</td>
			   <td><?php
		                 if ($returning_autho=='N')
			                {
			                  $returning_n="checked='checked'";
				              $returning_r="";
				              $returning_w="";
				              $returning_d="";
			                }
			             else
			             if ($returning_autho=='R')
			                {
			                  $returning_n="";
				              $returning_r="checked='checked'";
				              $returning_w="";
				              $returning_d="";
			                }
			             else
			             if ($returning_autho=='W')
			                {
			                  $returning_n="";
				              $returning_r="";
				              $returning_w="checked='checked'";
				              $returning_d="";
			                }
			             else
			             if ($returning_autho=='D')
			                {
			                  $returning_n="";
				              $returning_r="";
				              $returning_w="";
				              $returning_d="checked='checked'";
			                }   
		           ?>
                   <input name="rb_returning" id="rb_returning" type="radio" value="N" <?php echo $returning_n; ?>/>None&nbsp;
                   <input name="rb_returning" id="rb_returning" type="radio" value="R" <?php echo $returning_r; ?>/>Read&nbsp;
                   <input name="rb_returning" id="rb_returning" type="radio" value="W" <?php echo $returning_w; ?>/>Write&nbsp;
                   <input name="rb_returning" id="rb_returning" type="radio" value="D" <?php echo $returning_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aset Rusak</td>
			   <td>:</td>
			   <td><?php
		                if ($broken_autho=='N')
			               {
			                 $broken_n="checked='checked'";
				             $broken_r="";
				             $broken_w="";
				             $broken_d="";
			               }
			            else
			            if ($broken_autho=='R')
			               {
			                 $broken_n="";
				             $broken_r="checked='checked'";
				             $broken_w="";
				             $broken_d="";
			               }
			            else
			            if ($broken_autho=='W')
			               {
			                 $broken_n="";
				             $broken_r="";
				             $broken_w="checked='checked'";
				             $broken_d="";
			               }
			            else
			            if ($broken_autho=='D')
			               {
			                 $broken_n="";
				             $broken_r="";
				             $broken_w="";
				             $broken_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_broken" id="rb_broken" type="radio" value="N" <?php echo $broken_n; ?>/>None&nbsp;
                   <input name="rb_broken" id="rb_broken" type="radio" value="R" <?php echo $broken_r; ?>/>Read&nbsp;
                   <input name="rb_broken" id="rb_broken" type="radio" value="W" <?php echo $broken_w; ?>/>Write&nbsp;
                   <input name="rb_broken" id="rb_broken" type="radio" value="D" <?php echo $broken_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penghapusan Aset </td>
			   <td>:</td>
			   <td><?php
		                if ($write_off_autho=='N')
			               {
			                 $write_off_n="checked='checked'";
				             $write_off_r="";
				             $write_off_w="";
				             $write_off_d="";
			               }
			            else
			            if ($write_off_autho=='R')
			               {
			                 $write_off_n="";
				             $write_off_r="checked='checked'";
				             $write_off_w="";
				             $write_off_d="";
			               }
			            else
			            if ($write_off_autho=='W')
			               {
			                 $write_off_n="";
				             $write_off_r="";
				             $write_off_w="checked='checked'";
				             $write_off_d="";
			               }
			            else
			            if ($write_off_autho=='D')
			               {
			                 $write_off_n="";
				             $write_off_r="";
				             $write_off_w="";
				             $write_off_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_write_off" id="rb_write_off" type="radio" value="N" <?php echo $write_off_n; ?>/>None&nbsp;
                   <input name="rb_write_off" id="rb_write_off" type="radio" value="R" <?php echo $write_off_r; ?>/>Read&nbsp;
                   <input name="rb_write_off" id="rb_write_off" type="radio" value="W" <?php echo $write_off_w; ?>/>Write&nbsp;
                   <input name="rb_write_off" id="rb_write_off" type="radio" value="D" <?php echo $write_off_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penjualan Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($dispossal_autho=='N')
			               {
			                 $dispossal_n="checked='checked'";
				             $dispossal_r="";
				             $dispossal_w="";
				             $dispossal_d="";
			               }
			            else
			            if ($dispossal_autho=='R')
			               {
			                 $dispossal_n="";
				             $dispossal_r="checked='checked'";
				             $dispossal_w="";
				             $dispossal_d="";
			               }
			            else
			            if ($dispossal_autho=='W')
			               {
			                 $dispossal_n="";
				             $dispossal_r="";
				             $dispossal_w="checked='checked'";
				             $dispossal_d="";
			               }
			            else
			            if ($dispossal_autho=='D')
			               {
			                 $dispossal_n="";
				             $dispossal_r="";
				             $dispossal_w="";
				             $dispossal_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_dispossal" id="rb_dispossal" type="radio" value="N" <?php echo $dispossal_n; ?>/>None&nbsp;
                   <input name="rb_dispossal" id="rb_dispossal" type="radio" value="R" <?php echo $dispossal_r; ?>/>Read&nbsp;
                   <input name="rb_dispossal" id="rb_dispossal" type="radio" value="W" <?php echo $dispossal_w; ?>/>Write&nbsp;
                   <input name="rb_dispossal" id="rb_dispossal" type="radio" value="D" <?php echo $dispossal_d; ?>/>Delete				  </td>
	      </tr>
			 <tr>
			    <td width="43%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Perubahan Deskripsi Isi Aset </td>
				<td width="2%">:</td>
			    <td width="55%"><?php
		                             if ($change_description_autho=='N')
			                            {
			                              $change_description_n="checked='checked'";
				                          $change_description_r="";
				                          $change_description_w="";
				                          $change_description_d="";
			                            }
			                         else
			                         if ($change_description_autho=='R')
			                            {
			                              $change_description_n="";
				                          $change_description_r="checked='checked'";
				                          $change_description_w="";
				                          $change_description_d="";
			                            }
			                         else
			                         if ($change_description_autho=='W')
			                            {
			                              $change_description_n="";
				                          $change_description_r="";
				                          $change_description_w="checked='checked'";
				                          $change_description_d="";
			                            }
			                         else
			                         if ($change_description_autho=='D')
			                            {
			                              $change_description_n="";
				                          $change_description_r="";
				                          $change_description_w="";
				                          $change_description_d="checked='checked'";
			                            }   
		                        ?>
                                <input name="rb_change_description" id="rb_change_description" type="radio" value="N" <?php echo $change_description_n; ?>/>None&nbsp;
                                <input name="rb_change_description" id="rb_change_description" type="radio" value="R" <?php echo $change_description_r; ?>/>Read&nbsp;
                                <input name="rb_change_description" id="rb_change_description" type="radio" value="W" <?php echo $change_description_w; ?>/>Write&nbsp;
                                <input name="rb_change_description" id="rb_change_description" type="radio" value="D" <?php echo $change_description_d; ?>/>Delete			      </td>
			 </tr>
	  </table>	</td>
    <td colspan="3" valign="top">
	  <table border="0" width="100%" cellspacing="0" cellpadding="0" >
	         <tr>
			      <th colspan="3">LAPORAN</th>
			 </tr>
			 <tr>
			      <td width="40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Perpindahan Aset</td>
				  <td width="1%">:</td>
				  <td width="59%"><?php
		                               if ($transfer_report_autho=='N')
			                              {
			                                $transfer_report_n="checked='checked'";
				                            $transfer_report_r="";
				                            $transfer_report_w="";
				                            $transfer_report_d="";
			                              }
			                           else
			                           if ($transfer_report_autho=='R')
			                              {
			                                $transfer_report_n="";
				                            $transfer_report_r="checked='checked'";
				                            $transfer_report_w="";
				                            $transfer_report_d="";
			                              }
			                           else
			                           if ($transfer_report_autho=='W')
			                              {
			                                $transfer_report_n="";
				                            $transfer_report_r="";
				                            $transfer_report_w="checked='checked'";
				                            $transfer_report_d="";
			                              }
			                           else
			                           if ($transfer_report_autho=='D')
			                              {
			                                $transfer_report_n="";
				                            $transfer_report_r="";
				                            $transfer_report_w="";
				                            $transfer_report_d="checked='checked'";
			                              }   
		                          ?>
                                  <input name="rb_transfer_report" id="radio" type="radio" value="N" <?php echo $transfer_report_n; ?>/>None&nbsp;
                                  <input name="rb_transfer_report" id="radio" type="radio" value="R" <?php echo $transfer_report_r; ?>/>Read&nbsp;
                                  <input name="rb_transfer_report" id="radio" type="radio" value="W" <?php echo $transfer_report_w; ?>/>Write&nbsp;
                                  <input name="rb_transfer_report" id="radio" type="radio" value="D" <?php echo $transfer_report_d; ?>/>Delete					</td>
			 </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Pengeluaran Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($issuing_report_autho=='N')
			               {
			                 $issuing_report_n="checked='checked'";
				             $issuing_report_r="";
				             $issuing_report_w="";
				             $issuing_report_d="";
			               }
			            else
			            if ($issuing_report_autho=='R')
			               {
			                 $issuing_report_n="";
				             $issuing_report_r="checked='checked'";
				             $issuing_report_w="";
				             $issuing_report_d="";
			               }
			            else
			            if ($issuing_report_autho=='W')
			               {
			                 $issuing_report_n="";
				             $issuing_report_r="";
				             $issuing_report_w="checked='checked'";
				             $issuing_report_d="";
			               }
			            else
			            if ($issuing_report_autho=='D')
			               {
			                 $issuing_report_n="";
				             $issuing_report_r="";
				             $issuing_report_w="";
				             $issuing_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_issuing_report" id="rb_issuing_report" type="radio" value="N" <?php echo $issuing_report_n; ?>/>None&nbsp;
                   <input name="rb_issuing_report" id="rb_issuing_report" type="radio" value="R" <?php echo $issuing_report_r; ?>/>Read&nbsp;
                   <input name="rb_issuing_report" id="rb_issuing_report" type="radio" value="W" <?php echo $issuing_report_w; ?>/>Write&nbsp;
                   <input name="rb_issuing_report" id="rb_issuing_report" type="radio" value="D" <?php echo $issuing_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Pengembalian Aset</td>
			   <td>:</td>
			   <td><?php
		                 if ($returning_report_autho=='N')
			                {
			                  $returning_report_n="checked='checked'";
				              $returning_report_r="";
				              $returning_report_w="";
				              $returning_report_d="";
			                }
			             else
			             if ($returning_report_autho=='R')
			                {
			                  $returning_report_n="";
				              $returning_report_r="checked='checked'";
				              $returning_report_w="";
				              $returning_report_d="";
			                }
			             else
			             if ($returning_report_autho=='W')
			                {
			                  $returning_report_n="";
				              $returning_report_r="";
				              $returning_report_w="checked='checked'";
				              $returning_report_d="";
			                }
			             else
			             if ($returning_report_autho=='D')
			                {
			                  $returning_report_n="";
				              $returning_report_r="";
				              $returning_report_w="";
				              $returning_report_d="checked='checked'";
			                }   
		           ?>
                   <input name="rb_returning_report" id="rb_returning_report" type="radio" value="N" <?php echo $returning_report_n; ?>/>None&nbsp;
                   <input name="rb_returning_report" id="rb_returning_report" type="radio" value="R" <?php echo $returning_report_r; ?>/>Read&nbsp;
                   <input name="rb_returning_report" id="rb_returning_report" type="radio" value="W" <?php echo $returning_report_w; ?>/>Write&nbsp;
                   <input name="rb_returning_report" id="rb_returning_report" type="radio" value="D" <?php echo $returning_report_d; ?>/>Delete</td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Aset Rusak</td>
			   <td>:</td>
			   <td><?php
		                if ($broken_report_autho=='N')
			               {
			                 $broken_report_n="checked='checked'";
				             $broken_report_r="";
				             $broken_report_w="";
				             $broken_report_d="";
			               }
			            else
			            if ($broken_report_autho=='R')
			               {
			                 $broken_report_n="";
				             $broken_report_r="checked='checked'";
				             $broken_report_w="";
				             $broken_report_d="";
			               }
			            else
			            if ($broken_report_autho=='W')
			               {
			                 $broken_report_n="";
				             $broken_report_r="";
				             $broken_report_w="checked='checked'";
				             $broken_report_d="";
			               }
			            else
			            if ($broken_report_autho=='D')
			               {
			                 $broken_report_n="";
				             $broken_report_r="";
				             $broken_report_w="";
				             $broken_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_broken_report" id="rb_broken_report" type="radio" value="N" <?php echo $broken_report_n; ?>/>None&nbsp;
                   <input name="rb_broken_report" id="rb_broken_report" type="radio" value="R" <?php echo $broken_report_r; ?>/>Read&nbsp;
                   <input name="rb_broken_report" id="rb_broken_report" type="radio" value="W" <?php echo $broken_report_w; ?>/>Write&nbsp;
                   <input name="rb_broken_report" id="rb_broken_report" type="radio" value="D" <?php echo $broken_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Laporan Penghapusan Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($write_off_report_autho=='N')
			               {
			                 $write_off_report_n="checked='checked'";
				             $write_off_report_r="";
				             $write_off_report_w="";
				             $write_off_report_d="";
			               }
			            else
			            if ($write_off_report_autho=='R')
			               {
			                 $write_off_report_n="";
				             $write_off_report_r="checked='checked'";
				             $write_off_report_w="";
				             $write_off_report_d="";
			               }
			            else
			            if ($write_off_report_autho=='W')
			               {
			                 $write_off_report_n="";
				             $write_off_report_r="";
				             $write_off_report_w="checked='checked'";
				             $write_off_report_d="";
			               }
			            else
			            if ($write_off_report_autho=='D')
			               {
			                 $write_off_report_n="";
				             $write_off_report_r="";
				             $write_off_report_w="";
				             $write_off_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_write_off_report" id="rb_write_off_report" type="radio" value="N" <?php echo $write_off_report_n; ?>/>None&nbsp;
                   <input name="rb_write_off_report" id="rb_write_off_report" type="radio" value="R" <?php echo $write_off_report_r; ?>/>Read&nbsp;
                   <input name="rb_write_off_report" id="rb_write_off_report" type="radio" value="W" <?php echo $write_off_report_w; ?>/>Write&nbsp;
                   <input name="rb_write_off_report" id="rb_write_off_report" type="radio" value="D" <?php echo $write_off_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Penjualan Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($dispossal_report_autho=='N')
			               {
			                 $dispossal_report_n="checked='checked'";
				             $dispossal_report_r="";
				             $dispossal_report_w="";
				             $dispossal_report_d="";
			               }
			            else
			            if ($dispossal_report_autho=='R')
			               {
			                 $dispossal_report_n="";
				             $dispossal_report_r="checked='checked'";
				             $dispossal_report_w="";
				             $dispossal_report_d="";
			               }
			            else
			            if ($dispossal_report_autho=='W')
			               {
			                 $dispossal_report_n="";
				             $dispossal_report_r="";
				             $dispossal_report_w="checked='checked'";
				             $dispossal_report_d="";
			               }
			            else
			            if ($dispossal_report_autho=='D')
			               {
			                 $dispossal_report_n="";
				             $dispossal_report_r="";
				             $dispossal_report_w="";
				             $dispossal_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_dispossal_report" id="rb_dispossal_report" type="radio" value="N" <?php echo $dispossal_report_n; ?>/>None&nbsp;
                   <input name="rb_dispossal_report" id="rb_dispossal_report" type="radio" value="R" <?php echo $dispossal_report_r; ?>/>Read&nbsp;
                   <input name="rb_dispossal_report" id="rb_dispossal_report" type="radio" value="W" <?php echo $dispossal_report_w; ?>/>Write&nbsp;
                   <input name="rb_dispossal_report" id="rb_dispossal_report" type="radio" value="D" <?php echo $dispossal_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Perubahan Deskripsi Isi Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($cid_report_autho=='N')
			               {
			                 $cid_report_n="checked='checked'";
				             $cid_report_r="";
				             $cid_report_w="";
				             $cid_report_d="";
			               }
			            else
			            if ($cid_report_autho=='R')
			               {
			                 $cid_report_n="";
				             $cid_report_r="checked='checked'";
				             $cid_report_w="";
				             $cid_report_d="";
			               }
			            else
			            if ($cid_report_autho=='W')
			               {
			                 $cid_report_n="";
				             $cid_report_r="";
				             $cid_report_w="checked='checked'";
				             $cid_report_d="";
			               }
			            else
			            if ($cid_report_autho=='D')
			               {
			                 $cid_report_n="";
				             $cid_report_r="";
				             $cid_report_w="";
				             $cid_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_cid_report" id="rb_cid_report" type="radio" value="N" <?php echo $cid_report_n; ?>/>None&nbsp;
                   <input name="rb_cid_report" id="rb_cid_report" type="radio" value="R" <?php echo $cid_report_r; ?>/>Read&nbsp;
                   <input name="rb_cid_report" id="rb_cid_report" type="radio" value="W" <?php echo $cid_report_w; ?>/>Write&nbsp;
                   <input name="rb_cid_report" id="rb_cid_report" type="radio" value="D" <?php echo $cid_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Posisi Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($position_report_autho=='N')
			               {
			                 $position_report_n="checked='checked'";
				             $position_report_r="";
				             $position_report_w="";
				             $position_report_d="";
			               }
			            else
			            if ($position_report_autho=='R')
			               {
			                 $position_report_n="";
				             $position_report_r="checked='checked'";
				             $position_report_w="";
				             $position_report_d="";
			               }
			            else
			            if ($position_report_autho=='W')
			               {
			                 $position_report_n="";
				             $position_report_r="";
				             $position_report_w="checked='checked'";
				             $position_report_d="";
			               }
			            else
			            if ($position_report_autho=='D')
			               {
			                 $position_report_n="";
				             $position_report_r="";
				             $position_report_w="";
				             $position_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_position_report" id="rb_position_report" type="radio" value="N" <?php echo $position_report_n; ?>/>None&nbsp;
                   <input name="rb_position_report" id="rb_position_report" type="radio" value="R" <?php echo $position_report_r; ?>/>Read&nbsp;
                   <input name="rb_position_report" id="rb_position_report" type="radio" value="W" <?php echo $position_report_w; ?>/>Write&nbsp;
                   <input name="rb_position_report" id="rb_position_report" type="radio" value="D" <?php echo $position_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Aging Pelanggan</td>
			   <td>:</td>
			   <td><?php
		                if ($aging_report_autho=='N')
			               {
			                 $aging_report_n="checked='checked'";
				             $aging_report_r="";
				             $aging_report_w="";
				             $aging_report_d="";
			               }
			            else
			            if ($aging_report_autho=='R')
			               {
			                 $aging_report_n="";
				             $aging_report_r="checked='checked'";
				             $aging_report_w="";
				             $aging_report_d="";
			               }
			            else
			            if ($aging_report_autho=='W')
			               {
			                 $aging_report_n="";
				             $aging_report_r="";
				             $aging_report_w="checked='checked'";
				             $aging_report_d="";
			               }
			            else
			            if ($aging_report_autho=='D')
			               {
			                 $aging_report_n="";
				             $aging_report_r="";
				             $aging_report_w="";
				             $aging_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_aging_report" id="rb_aging_report" type="radio" value="N" <?php echo $aging_report_n; ?>/>None&nbsp;
                   <input name="rb_aging_report" id="rb_aging_report" type="radio" value="R" <?php echo $aging_report_r; ?>/>Read&nbsp;
                   <input name="rb_aging_report" id="rb_aging_report" type="radio" value="W" <?php echo $aging_report_w; ?>/>Write&nbsp;
                   <input name="rb_aging_report" id="rb_aging_report" type="radio" value="D" <?php echo $aging_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan SOA</td>
			   <td>&nbsp;</td>
			   <td><?php
		                if ($soa_report_autho=='N')
			               {
			                 $soa_report_n="checked='checked'";
				             $soa_report_r="";
				             $soa_report_w="";
				             $soa_report_d="";
			               }
			            else
			            if ($soa_report_autho=='R')
			               {
			                 $soa_report_n="";
				             $soa_report_r="checked='checked'";
				             $soa_report_w="";
				             $soa_report_d="";
			               }
			            else
			            if ($soa_report_autho=='W')
			               {
			                 $soa_report_n="";
				             $soa_report_r="";
				             $soa_report_w="checked='checked'";
				             $soa_report_d="";
			               }
			            else
			            if ($soa_report_autho=='D')
			               {
			                 $soa_report_n="";
				             $soa_report_r="";
				             $soa_report_w="";
				             $soa_report_d="checked='checked'";
			               }   
		           ?>
                 <input name="rb_soa_report" id="rb_soa_report" type="radio" value="N" <?php echo $soa_report_n; ?>/>None&nbsp;
                 <input name="rb_soa_report" id="rb_soa_report" type="radio" value="R" <?php echo $soa_report_r; ?>/>Read&nbsp;
                 <input name="rb_soa_report" id="rb_soa_report" type="radio" value="W" <?php echo $soa_report_w; ?>/>Write&nbsp;
                 <input name="rb_soa_report" id="rb_soa_report" type="radio" value="D" <?php echo $soa_report_d; ?>/>Delete</td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Pengeluaran Aset Ke Vendor</td>
			   <td>:</td>
			   <td><?php
		                if ($vendor_item_report_autho=='N')
			               {
			                 $vendor_item_report_n="checked='checked'";
				             $vendor_item_report_r="";
				             $vendor_item_report_w="";
				             $vendor_item_report_d="";
			               }
			            else
			            if ($vendor_item_report_autho=='R')
			               {
			                 $vendor_item_report_n="";
				             $vendor_item_report_r="checked='checked'";
				             $vendor_item_report_w="";
				             $vendor_item_report_d="";
			               }
			            else
			            if ($vendor_item_report_autho=='W')
			               {
			                 $vendor_item_report_n="";
				             $vendor_item_report_r="";
				             $vendor_item_report_w="checked='checked'";
				             $vendor_item_report_d="";
			               }
			            else
			            if ($vendor_item_report_autho=='D')
			               {
			                 $vendor_item_report_n="";
				             $vendor_item_report_r="";
				             $vendor_item_report_w="";
				             $vendor_item_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_vendor_item_report" id="rb_vendor_item_report" type="radio" value="N" <?php echo $vendor_item_report_n; ?>/>None&nbsp;
                   <input name="rb_vendor_item_report" id="rb_vendor_item_report" type="radio" value="R" <?php echo $vendor_item_report_r; ?>/>Read&nbsp;
                   <input name="rb_vendor_item_report" id="rb_vendor_item_report" type="radio" value="W" <?php echo $vendor_item_report_w; ?>/>Write&nbsp;
                   <input name="rb_vendor_item_report" id="rb_vendor_item_report" type="radio" value="D" <?php echo $vendor_item_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Histori Pergerakan Aset</td>
			   <td>:</td>
			   <td><?php
		                if ($history_movement_report_autho=='N')
			               {
			                 $history_movement_report_n="checked='checked'";
				             $history_movement_report_r="";
				             $history_movement_report_w="";
				             $history_movement_report_d="";
			               }
			            else
			            if ($history_movement_report_autho=='R')
			               {
			                 $history_movement_report_n="";
				             $history_movement_report_r="checked='checked'";
				             $history_movement_report_w="";
				             $history_movement_report_d="";
			               }
			            else
			            if ($history_movement_report_autho=='W')
			               {
			                 $history_movement_report_n="";
				             $history_movement_report_r="";
				             $history_movement_report_w="checked='checked'";
				             $history_movement_report_d="";
			               }
			            else
			            if ($history_movement_report_autho=='D')
			               {
			                 $history_movement_report_n="";
				             $history_movement_report_r="";
				             $history_movement_report_w="";
				             $history_movement_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_history_movement_report" id="rb_history_movement_report" type="radio" value="N" <?php echo $history_movement_report_n; ?>/>None&nbsp;
                   <input name="rb_history_movement_report" id="rb_history_movement_report" type="radio" value="R" <?php echo $history_movement_report_r; ?>/>Read&nbsp;
                   <input name="rb_history_movement_report" id="rb_history_movement_report" type="radio" value="W" <?php echo $history_movement_report_w; ?>/>Write&nbsp;
                   <input name="rb_history_movement_report" id="rb_history_movement_report" type="radio" value="D" <?php echo $history_movement_report_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laporan Histori Transaksi </td>
			   <td>:</td>
			   <td><?php
		                if ($doc_flow_report_autho=='N')
			               {
			                 $doc_flow_report_n="checked='checked'";
				             $doc_flow_report_r="";
				             $doc_flow_report_w="";
				             $doc_flow_report_d="";
			               }
			            else
			            if ($doc_flow_report_autho=='R')
			               {
			                 $doc_flow_report_n="";
				             $doc_flow_report_r="checked='checked'";
				             $doc_flow_report_w="";
				             $doc_flow_report_d="";
			               }
			            else
			            if ($doc_flow_report_autho=='W')
			               {
			                 $doc_flow_report_n="";
				             $doc_flow_report_r="";
				             $doc_flow_report_w="checked='checked'";
				             $doc_flow_report_d="";
			               }
			            else
			            if ($doc_flow_report_autho=='D')
			               {
			                 $doc_flow_report_n="";
				             $doc_flow_report_r="";
				             $doc_flow_report_w="";
				             $doc_flow_report_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_doc_flow_report" id="rb_doc_flow_report" type="radio" value="N" <?php echo $doc_flow_report_n; ?>/>None&nbsp;
                   <input name="rb_doc_flow_report" id="rb_doc_flow_report" type="radio" value="R" <?php echo $doc_flow_report_r; ?>/>Read&nbsp;
                   <input name="rb_doc_flow_report" id="rb_doc_flow_report" type="radio" value="W" <?php echo $doc_flow_report_w; ?>/>Write&nbsp;
                   <input name="rb_doc_flow_report" id="rb_doc_flow_report" type="radio" value="D" <?php echo $doc_flow_report_d; ?>/>Delete			 </td>
	      </tr>
	  </table>
	  <br />
	  <table border="0" width="100%" cellspacing="0" cellpadding="0" >
	         <tr>
			      <th colspan="3">SETTING</th>
			 </tr>
			 <tr>
			      <td width="40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Karyawan</td>
				  <td width="1%">:</td>
				  <td width="59%"><?php
		                               if ($employee_autho=='N')
			                              {
			                                $employee_n="checked='checked'";
				                            $employee_r="";
				                            $employee_w="";
				                            $employee_d="";
			                              }
			                           else
			                           if ($employee_autho=='R')
			                              {
			                                $employee_n="";
				                            $employee_r="checked='checked'";
				                            $employee_w="";
				                            $employee_d="";
			                              }
			                           else
			                           if ($employee_autho=='W')
			                              {
			                                $employee_n="";
				                            $employee_r="";
				                            $employee_w="checked='checked'";
				                            $employee_d="";
			                              }
			                           else
			                           if ($employee_autho=='D')
			                              {
			                                $employee_n="";
				                            $employee_r="";
				                            $employee_w="";
				                            $employee_d="checked='checked'";
			                              }   
		                          ?>
		                          <input name="rb_employee" id="rb_employee" type="radio" value="N" <?php echo $employee_n; ?>/>None&nbsp;
                                  <input name="rb_employee" id="rb_employee" type="radio" value="R" <?php echo $employee_r; ?>/>Read&nbsp;
                                  <input name="rb_employee" id="rb_employee" type="radio" value="W" <?php echo $employee_w; ?>/>Write&nbsp;
                                  <input name="rb_employee" id="rb_employee" type="radio" value="D" <?php echo $employee_d; ?>/>Delete</td>
			 </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Satuan Unit </td>
			   <td>:</td>
			   <td><?php
		                 if ($uom_autho=='N')
			                {
			                  $uom_n="checked='checked'";
				              $uom_r="";
				              $uom_w="";
				              $uom_d="";
			                }
			             else
			             if ($uom_autho=='R')
			                {
			                  $uom_n="";
				              $uom_r="checked='checked'";
				              $uom_w="";
				              $uom_d="";
			                }
			             else
			             if ($uom_autho=='W')
			                {
			                  $uom_n="";
				              $uom_r="";
				              $uom_w="checked='checked'";
				              $uom_d="";
			                }
			             else
			             if ($uom_autho=='D')
			                {
			                  $uom_n="";
				              $uom_r="";
				              $uom_w="";
				              $uom_d="checked='checked'";
			                }   
		           ?>
                   <input name="rb_uom" id="rb_uom" type="radio" value="N" <?php echo $uom_n; ?>/>None&nbsp;
                   <input name="rb_uom" id="rb_uom" type="radio" value="R" <?php echo $uom_r; ?>/>Read&nbsp;
                   <input name="rb_uom" id="rb_uom" type="radio" value="W" <?php echo $uom_w; ?>/>Write&nbsp;
                   <input name="rb_uom" id="rb_uom" type="radio" value="D" <?php echo $uom_d; ?>/>Delete			   </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tipe Pelanggan </td>
			   <td>:</td>
			   <td><?php
		                 if ($customer_type_autho=='N')
			                {
			                  $customer_type_n="checked='checked'";
				              $customer_type_r="";
				              $customer_type_w="";
				              $customer_type_d="";
			                }
			             else
			             if ($customer_type_autho=='R')
			                {
			                  $customer_type_n="";
				              $customer_type_r="checked='checked'";
				              $customer_type_w="";
				              $customer_type_d="";
			                }
			             else
			             if ($customer_type_autho=='W')
			                {
			                  $customer_type_n="";
				              $customer_type_r="";
				              $customer_type_w="checked='checked'";
				              $customer_type_d="";
			                }
			             else
			             if ($customer_type_autho=='D')
			                {
			                  $customer_type_n="";
				              $customer_type_r="";
				              $customer_type_w="";
				              $customer_type_d="checked='checked'";
			                }   
		           ?>
                 <input name="rb_customer_type" id="rb_customer_type" type="radio" value="N" <?php echo $customer_type_n; ?>/>None&nbsp;
                 <input name="rb_customer_type" id="rb_customer_type" type="radio" value="R" <?php echo $customer_type_r; ?>/>Read&nbsp;
                 <input name="rb_customer_type" id="rb_customer_type" type="radio" value="W" <?php echo $customer_type_w; ?>/>Write&nbsp;
                 <input name="rb_customer_type" id="rb_customer_type" type="radio" value="D" <?php echo $customer_type_d; ?>/>Delete</td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelanggan</td>
			   <td>:</td>
			   <td><?php
		                if ($customer_autho=='N')
			               {
			                 $customer_n="checked='checked'";
				             $customer_r="";
				             $customer_w="";
				             $customer_d="";
			               }
			            else
			            if ($customer_autho=='R')
			               {
			                 $customer_n="";
				             $customer_r="checked='checked'";
				             $customer_w="";
				             $customer_d="";
			               }
			            else
			            if ($customer_autho=='W')
			               {
			                 $customer_n="";
				             $customer_r="";
				             $customer_w="checked='checked'";
				             $customer_d="";
			               }
			            else
			            if ($customer_autho=='D')
			               {
			                 $customer_n="";
				             $customer_r="";
				             $customer_w="";
				             $customer_d="checked='checked'";
			               }   
		           ?>
                   <input name="rb_customer" id="rb_customer" type="radio" value="N" <?php echo $customer_n; ?>/>None&nbsp;
                   <input name="rb_customer" id="rb_customer" type="radio" value="R" <?php echo $customer_r; ?>/>Read&nbsp;
                   <input name="rb_customer" id="rb_customer" type="radio" value="W" <?php echo $customer_w; ?>/>Write&nbsp;
                   <input name="rb_customer" id="rb_customer" type="radio" value="D" <?php echo $customer_d; ?>/>Delete				  </td>
	      </tr>
			 <tr>
			   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vendor</td>
			   <td>:</td>
			   <td><?php
		                                if ($vendor_autho=='N')
			                               {
			                                 $vendor_n="checked='checked'";
				                             $vendor_r="";
				                             $vendor_w="";
				                             $vendor_d="";
			                               }
			                            else
			                            if ($vendor_autho=='R')
			                               {
			                                 $vendor_n="";
				                             $vendor_r="checked='checked'";
				                             $vendor_w="";
				                             $vendor_d="";
			                               }
			                            else
			                            if ($vendor_autho=='W')
			                               {
			                                 $vendor_n="";
				                             $vendor_r="";
				                             $vendor_w="checked='checked'";
				                             $vendor_d="";
			                               }
			                            else
			                            if ($vendor_autho=='D')
			                               {
			                                 $vendor_n="";
				                             $vendor_r="";
				                             $vendor_w="";
				                             $vendor_d="checked='checked'";
			                               }   
		                          ?>
                 <input name="rb_vendor" id="rb_vendor" type="radio" value="N" <?php echo $vendor_n; ?>/>None&nbsp;
                 <input name="rb_vendor" id="rb_vendor" type="radio" value="R" <?php echo $vendor_r; ?>/>Read&nbsp;
                 <input name="rb_vendor" id="rb_vendor" type="radio" value="W" <?php echo $vendor_w; ?>/>Write&nbsp;
               <input name="rb_vendor" id="rb_vendor" type="radio" value="D" <?php echo $vendor_d; ?>/>Delete</td>
	      </tr>
			 <tr>
			      <td width="40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Period Transaksi</td>
				  <td width="1%">:</td>
				  <td width="59%"><?php
		                                if ($apt_period_autho=='N')
			                               {
			                                 $apt_period_n="checked='checked'";
				                             $apt_period_r="";
				                             $apt_period_w="";
				                             $apt_period_d="";
			                               }
			                            else
			                            if ($apt_period_autho=='R')
			                               {
			                                 $apt_period_n="";
				                             $apt_period_r="checked='checked'";
				                             $apt_period_w="";
				                             $apt_period_d="";
			                               }
			                            else
			                            if ($apt_period_autho=='W')
			                               {
			                                 $apt_period_n="";
				                             $apt_period_r="";
				                             $apt_period_w="checked='checked'";
				                             $apt_period_d="";
			                               }
			                            else
			                            if ($apt_period_autho=='D')
			                               {
			                                 $apt_period_n="";
				                             $apt_period_r="";
				                             $apt_period_w="";
				                             $apt_period_d="checked='checked'";
			                               }   
		                          ?>
                    <input name="rb_apt_period" id="rb_apt_period" type="radio" value="N" <?php echo $apt_period_n; ?>/>None&nbsp;
                    <input name="rb_apt_period" id="rb_apt_period" type="radio" value="R" <?php echo $apt_period_r; ?>/>Read&nbsp;
                    <input name="rb_apt_period" id="rb_apt_period" type="radio" value="W" <?php echo $apt_period_w; ?>/>Write&nbsp;
                    <input name="rb_apt_period" id="rb_apt_period" type="radio" value="D" <?php echo $apt_period_d; ?>/>Delete</td>
			 </tr>
	  </table>
	</td>
  </tr>
</table>
</form>

<?php
 
 if (isset($_POST['btn_save']))
    {
      $rb_branch=$_POST['rb_branch'];
	  $rb_whs=$_POST['rb_whs'];
	  $rb_ctg=$_POST['rb_ctg'];
	  $rb_masti=$_POST['rb_masti'];
      $rb_itemd=$_POST['rb_itemd'];
	  $rb_itemd_all=$_POST['rb_itemd_all'];
	  $rb_summary_item=$_POST['rb_summary_item'];
	  $rb_transfer=$_POST['rb_transfer'];
	  $rb_receipt_transfer=$_POST['rb_receipt_transfer'];
	  $rb_issuing=$_POST['rb_issuing'];
	  $rb_returning=$_POST['rb_returning'];
	  $rb_broken=$_POST['rb_broken'];
	  $rb_write_off=$_POST['rb_write_off'];
	  $rb_dispossal=$_POST['rb_dispossal'];
	  $rb_change_description=$_POST['rb_change_description'];
	  $rb_issuing_report=$_POST['rb_issuing_report'];
	  $rb_returning_report=$_POST['rb_returning_report'];
	  $rb_broken_report=$_POST['rb_broken_report'];
	  $rb_write_off_report=$_POST['rb_write_off_report'];
	  $rb_dispossal_report=$_POST['rb_dispossal_report'];
	  $rb_cid_report=$_POST['rb_cid_report'];
	  $rb_position_report=$_POST['rb_position_report'];
	  $rb_aging_report=$_POST['rb_aging_report'];
	  $rb_soa_report=$_POST['rb_soa_report'];
	  $rb_vendor_item_report=$_POST['rb_vendor_item_report'];
	  $rb_history_movement_report=$_POST['rb_history_movement_report'];
	  $rb_transfer_report=$_POST['rb_transfer_report'];
	  $rb_doc_flow_report=$_POST['rb_doc_flow_report'];
	  $rb_employee=$_POST['rb_employee'];
	  $rb_uom=$_POST['rb_uom'];
      $rb_customer_type=$_POST['rb_customer_type'];
	  $rb_customer=$_POST['rb_customer'];
      $rb_vendor=$_POST['rb_vendor']; 
	  $rb_apt_period=$_POST['rb_apt_period']; 
	  $data_branch=$_POST['cb_data'];
	  $setting_type=$_POST['rb_setting_type'];
	  $total_select_branch=count($data_branch);
	  if ($total_select_branch==0)
	     {
		   ?>
              <script language="javascript">
				alert('Silahkan Pilih Kantor Cabang!');
				window.location.href='javascript:history.back(1)';
			  </script>
		   <?php
		 }
	  else
	  if ($total_select_branch>1 && $setting_type==0)
	     {
		   ?>
              <script language="javascript">
				alert('Tipe Settingan User yang dipilih harus "Semua" !');
				window.location.href='javascript:history.back(1)';
			  </script>
		   <?php 
		 }
	  else
	     {	         
		    if ($total_select_branch==1)
			   {
			     insert_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_itemd_all, $rb_summary_item, $rb_transfer, $rb_receipt_transfer, 
					                          $rb_issuing, $rb_returning, $rb_broken, 
					                          $rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, $rb_broken_report, 
										      $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, $rb_soa_report, 
										      $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, $rb_employee, $rb_uom, 
										      $rb_customer_type, $rb_customer, $rb_vendor, $rb_apt_period, $setting_type, $id, $db_connection, $data_branch[0]);
			    /*$q_check_authorization_user="SELECT * FROM users_authorization WHERE users_id='$id' AND branch_id='$data_branch[0]'";  
				 $exec_check_authorization_user=mysqli_query($db_connection, $q_check_authorization_user);
				 if (mysqli_num_rows($exec_check_authorization_user)==0)
				    {
					  insert_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_summary_item, $rb_transfer, $rb_receipt_transfer, 
					                               $rb_issuing, $rb_returning, $rb_broken, 
					                               $rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, $rb_broken_report, 
												   $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, $rb_soa_report, 
												   $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, $rb_employee, $rb_uom, 
												   $rb_customer_type, $rb_customer, $rb_vendor, $setting_type, $id, $db_connection, $data_branch[0]);
					}
			     else
				    {
					  update_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_summary_item, $rb_transfer, $rb_receipt_transfer, 
					                               $rb_issuing, $rb_returning, $rb_broken, 
					                               $rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, $rb_broken_report, 
												   $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, $rb_soa_report,
												   $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, $rb_employee, $rb_uom, 
												   $rb_customer_type, $rb_customer, $rb_vendor, $setting_type, $id, $db_connection, $data_branch[0]);
					}		  */
			   }
			else
			   {
			     foreach ($data_branch as $branch_id)
				        {
						  insert_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_itemd_all, $rb_summary_item, $rb_transfer,$rb_receipt_transfer,
							                           $rb_issuing, $rb_returning, 
							                           $rb_broken,$rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, 
													   $rb_broken_report, $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, 
													   $rb_soa_report, $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, 
													   $rb_employee, $rb_uom, $rb_customer_type, $rb_customer, $rb_vendor, $rb_apt_period, $setting_type, $id, 
													   $db_connection, $branch_id);
						 /* $q_check_authorization_user="SELECT * FROM users_authorization WHERE users_id='$id' AND branch_id='$branch_id'";  
				          $exec_check_authorization_user=mysqli_query($db_connection, $q_check_authorization_user);
				          if (mysqli_num_rows($exec_check_authorization_user)==0)
				             {
					           insert_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_summary_item, $rb_transfer,$rb_receipt_transfer,
							                                $rb_issuing, $rb_returning, 
							                                $rb_broken,$rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, 
															$rb_broken_report, $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, 
															$rb_soa_report, $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, 
															$rb_employee, $rb_uom, $rb_customer_type, $rb_customer, $rb_vendor, $setting_type, $id, $db_connection, $branch_id);
					         }
			              else
				             {
					           update_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_summary_item, $rb_transfer, $rb_receipt_transfer,
							                                $rb_issuing, $rb_returning, 
							                                $rb_broken,$rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, 
															$rb_broken_report, $rb_write_off_report, $rb_dispossal_report, $rb_cid_report, $rb_position_report, $rb_aging_report, 
															$rb_soa_report, $rb_vendor_item_report, $rb_history_movement_report, $rb_transfer_report,  $rb_doc_flow_report, 
															$rb_employee, $rb_uom, $rb_customer_type, $rb_customer, $rb_vendor, $setting_type, $id, $db_connection, $branch_id);
					         }	 */
						} 
			   } 
		 }  
    }
	
function insert_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_itemd_all, $rb_summary_item, $rb_transfer, $rb_receipt_transfer, $rb_issuing, 
                                      $rb_returning, $rb_broken, 
                                      $rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, $rb_broken_report, $rb_write_off_report, 
									  $rb_dispossal_report, $rb_cid_report,$rb_position_report, $rb_aging_report, $rb_soa_report, $rb_vendor_item_report, 
									  $rb_history_movement_report, $rb_transfer_report, $rb_doc_flow_report, $rb_employee, $rb_uom, $rb_customer_type, $rb_customer, $rb_vendor, 
									  $rb_apt_period, $setting_type, $id, $db_connection, $branch_id)
		  {
		    mysqli_autocommit($db_connection, false);
			$is_continue=1;
		    $q_check_authorization_user="SELECT * FROM users_authorization WHERE users_id='$id' AND branch_id='$branch_id'";  
			$exec_check_authorization_user=mysqli_query($db_connection, $q_check_authorization_user);
		    if (mysqli_num_rows($exec_check_authorization_user)>0)
		       {
			     $q_delete_authorization="DELETE FROM users_authorization WHERE users_id='$id' AND branch_id='$branch_id'";
				 $exec_delete_authorization=mysqli_query($db_connection, $q_delete_authorization);
				 if (!$exec_delete_authorization)
				    {
					  $is_continue=0;
			          mysqli_query($db_connection, 'rollback')
		              ?>
	                    <script language="javascript">
			              alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
	                      window.location.href='javascript:history.back(1)';
	                    </script>
	                  <?php 					  
					}
			   }
            if ($is_continue==1)
			   {	
			     $q_insert_data="INSERT INTO users_authorization (branch_id, users_id, menu, authorization) VALUES ".
			                    "('".$branch_id."','".$id."','BRANCH','".$rb_branch."'),".
							    "('".$branch_id."','".$id."','WHS','".$rb_whs."'),".
							    "('".$branch_id."','".$id."','ITEM_CATEGORY','".$rb_ctg."'),". 
							    "('".$branch_id."','".$id."','MASTER_ITEM','".$rb_masti."'),". 
							    "('".$branch_id."','".$id."','ITEM_DETAIL','".$rb_itemd."'),". 
							    "('".$branch_id."','".$id."','ITEM_DETAIL_ALL','".$rb_itemd."'),". 
							    "('".$branch_id."','".$id."','SUMMARY_ITEM','".$rb_summary_item."'),". 
							    "('".$branch_id."','".$id."','TRANSFER_ITEM','".$rb_transfer."'),". 
							    "('".$branch_id."','".$id."','RECEIPT_TRANSFER_ITEM','".$rb_receipt_transfer."'),". 
							    "('".$branch_id."','".$id."','ISSUING','".$rb_issuing."'),". 
							    "('".$branch_id."','".$id."','RETURNING','".$rb_returning."'),". 
							    "('".$branch_id."','".$id."','BROKEN','".$rb_broken."'),". 
							    "('".$branch_id."','".$id."','WRITE_OFF','".$rb_write_off."'),". 
							    "('".$branch_id."','".$id."','DISPOSSAL','".$rb_dispossal."'),". 
							    "('".$branch_id."','".$id."','CHANGE_DESCRIPTION','".$rb_change_description."'),". 
							    "('".$branch_id."','".$id."','ISSUING_REPORT','".$rb_issuing_report."'),". 
							    "('".$branch_id."','".$id."','RETURNING_REPORT','".$rb_returning_report."'),". 
							    "('".$branch_id."','".$id."','BROKEN_REPORT','".$rb_broken_report."'),". 
							    "('".$branch_id."','".$id."','WRITE_OFF_REPORT','".$rb_write_off_report."'),". 
					            "('".$branch_id."','".$id."','DISPOSSAL_REPORT','".$rb_dispossal_report."'),". 
							    "('".$branch_id."','".$id."','CHANGE_DESCRIPTION_REPORT','".$rb_cid_report."'),". 
							    "('".$branch_id."','".$id."','POSITION_REPORT','".$rb_position_report."'),". 
							    "('".$branch_id."','".$id."','AGING_REPORT','".$rb_aging_report."'),". 
							    "('".$branch_id."','".$id."','SOA_REPORT','".$rb_soa_report."'),". 
							    "('".$branch_id."','".$id."','VENDOR_ITEM_REPORT','".$rb_vendor_item_report."'),". 
							    "('".$branch_id."','".$id."','HISTORY_MOVEMENT_REPORT','".$rb_history_movement_report."'),". 
							    "('".$branch_id."','".$id."','TRANSFER_REPORT','".$rb_transfer_report."'),". 
							    "('".$branch_id."','".$id."','DOC_FLOW_REPORT','".$rb_doc_flow_report."'),". 
							    "('".$branch_id."','".$id."','EMPLOYEE','".$rb_employee."'),". 
							    "('".$branch_id."','".$id."','UOM','".$rb_uom."'),". 
							    "('".$branch_id."','".$id."','CUSTOMER_TYPE','".$rb_customer_type."'),". 
							    "('".$branch_id."','".$id."','CUSTOMER','".$rb_customer."'),". 
								"('".$branch_id."','".$id."','APT_PERIOD','".$rb_apt_period."'),". 
							    "('".$branch_id."','".$id."','VENDOR','".$rb_vendor."')";
			    $exec_insert_data=mysqli_query($db_connection, $q_insert_data);
		        if ($exec_insert_data)
			       {
			         mysqli_commit($db_connection);
				     ?>
                        <script language="javascript">
				          opener.location.reload(true);
				          window.close();
			            </script>
		             <?php 
			       }
			    else
			       {
			         mysqli_query($db_connection, 'rollback')
		             ?>
	                   <script language="javascript">
			             alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
	                     window.location.href='javascript:history.back(1)';
	                   </script>
	                 <?php    
				   }
			   }     
		  }	
	
 /*	
 function update_authorizationrization($rb_branch, $rb_whs, $rb_ctg, $rb_masti, $rb_itemd, $rb_summary_item, $rb_transfer, $rb_receipt_transfer, $rb_issuing, 
                                       $rb_returning, $rb_broken, 
                                       $rb_write_off, $rb_dispossal, $rb_change_description, $rb_issuing_report, $rb_returning_report, $rb_broken_report, $rb_write_off_report, 
									   $rb_dispossal_report,$rb_cid_report, $rb_position_report, $rb_aging_report, $rb_soa_report, $rb_vendor_item_report, 
									   $rb_history_movement_report,  $rb_transfer_report, $rb_doc_flow_report, $rb_employee, $rb_uom, $rb_customer_type, $rb_customer, $rb_vendor, 
									   $setting_type, $id, $db_connection, $branch_id)									   
		  {
            mysqli_autocommit($db_connection, false);  
			$q_update_1="UPDATE users_authorization SET authorization='$rb_branch' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='BRANCH'";
            $exec_update_1=mysqli_query($db_connection, $q_update_1);
			$q_update_2="UPDATE users_authorization SET authorization='$rb_whs' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='WHS'";
            $exec_update_2=mysqli_query($db_connection, $q_update_2);
			$q_update_3="UPDATE users_authorization SET authorization='$rb_ctg' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='ITEM_CATEGORY'";
            $exec_update_3=mysqli_query($db_connection, $q_update_3);
			$q_update_4="UPDATE users_authorization SET authorization='$rb_masti' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='MASTER_ITEM'";
            $exec_update_4=mysqli_query($db_connection, $q_update_4);
            $q_update_5="UPDATE users_authorization SET authorization='$rb_itemd' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='ITEM_DETAIL'";
            $exec_update_5=mysqli_query($db_connection, $q_update_5);
			$q_update_51="UPDATE users_authorization SET authorization='$rb_itemd_all' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='ITEM_DETAIL_ALL'";
            //$exec_update_51=mysqli_query($db_connection, $q_update_51);
			//$q_update_6="UPDATE users_authorization SET authorization='$rb_summary_item' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='SUMMARY_ITEM'";
            $exec_update_6=mysqli_query($db_connection, $q_update_6);
			$q_update_7="UPDATE users_authorization SET authorization='$rb_transfer' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='TRANSFER_ITEM'";
            $exec_update_7=mysqli_query($db_connection, $q_update_7);
			$q_update_8="UPDATE users_authorization SET authorization='$rb_receipt_transfer' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='RECEIPT_TRANSFER_ITEM'";
            $exec_update_8=mysqli_query($db_connection, $q_update_8);
			$q_update_9="UPDATE users_authorization SET authorization='$rb_issuing' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='ISSUING'";
            $exec_update_9=mysqli_query($db_connection, $q_update_9);
			$q_update_10="UPDATE users_authorization SET authorization='$rb_returning' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='RETURNING'";
            $exec_update_10=mysqli_query($db_connection, $q_update_10);
			$q_update_11="UPDATE users_authorization SET authorization='$rb_broken' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='BROKEN'";
            $exec_update_11=mysqli_query($db_connection, $q_update_11);
			$q_update_12="UPDATE users_authorization SET authorization='$rb_write_off' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='WRITE_OFF'";
            $exec_update_12=mysqli_query($db_connection, $q_update_12);
            $q_update_13="UPDATE users_authorization SET authorization='$rb_dispossal' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='DISPOSSAL'";
            $exec_update_13=mysqli_query($db_connection, $q_update_13);
			$q_update_14="UPDATE users_authorization SET authorization='$rb_change_description' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='CHANGE_DESCRIPTION'";
            $exec_update_14=mysqli_query($db_connection, $q_update_14);
			$q_update_15="UPDATE users_authorization SET authorization='$rb_issuing_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='ISSUING_REPORT'";
            $exec_update_15=mysqli_query($db_connection, $q_update_15);
			$q_update_16="UPDATE users_authorization SET authorization='$rb_returning_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='RETURNING_REPORT'";
            $exec_update_16=mysqli_query($db_connection, $q_update_16);
			$q_update_17="UPDATE users_authorization SET authorization='$rb_broken_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='BROKEN_REPORT'";
            $exec_update_17=mysqli_query($db_connection, $q_update_17);
			$q_update_18="UPDATE users_authorization SET authorization='$rb_write_off_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='WRITE_OFF_REPORT'";
            $exec_update_18=mysqli_query($db_connection, $q_update_18);
            $q_update_19="UPDATE users_authorization SET authorization='$rb_dispossal_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='DISPOSSAL_REPORT'";
            $exec_update_19=mysqli_query($db_connection, $q_update_19);
			$q_update_20="UPDATE users_authorization SET authorization='$rb_cid_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='CHANGE_DESCRIPTION_REPORT'";
            $exec_update_20=mysqli_query($db_connection, $q_update_20);
			$q_update_21="UPDATE users_authorization SET authorization='$rb_position_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='POSITION_REPORT'";
            $exec_update_21=mysqli_query($db_connection, $q_update_21);
			$q_update_22="UPDATE users_authorization SET authorization='$rb_aging_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='AGING_REPORT'";
            $exec_update_22=mysqli_query($db_connection, $q_update_22);
			$q_update_23="UPDATE users_authorization SET authorization='$rb_vendor_item_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='VENDOR_ITEM_REPORT'";
            $exec_update_23=mysqli_query($db_connection, $q_update_23);
            $q_update_24="UPDATE users_authorization SET authorization='$rb_history_movement_report' WHERE users_id='$id' AND branch_id='$branch_id' AND 
			                     menu='HISTORY_MOVEMENT_REPORT'";
            $exec_update_24=mysqli_query($db_connection, $q_update_24);
			$q_update_25="UPDATE users_authorization SET authorization='$rb_transfer_report' WHERE users_id='$id' AND branch_id='$branch_id' AND 
			                     menu='TRANSFER_REPORT'";
            $exec_update_25=mysqli_query($db_connection, $q_update_25);
			$q_update_26="UPDATE users_authorization SET authorization='$rb_doc_flow_report' WHERE users_id='$id' AND branch_id='$branch_id' AND 
			                     menu='DOC_FLOW_REPORT'";
            $exec_update_26=mysqli_query($db_connection, $q_update_26);
			$q_update_27="UPDATE users_authorization SET authorization='$rb_employee' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='EMPLOYEE'";
            $exec_update_27=mysqli_query($db_connection, $q_update_27);
            $q_update_28="UPDATE users_authorization SET authorization='$rb_uom' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='UOM'";
            $exec_update_28=mysqli_query($db_connection, $q_update_28);
			$q_update_29="UPDATE users_authorization SET authorization='$rb_customer_type' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='CUSTOMER_TYPE'";
            $exec_update_29=mysqli_query($db_connection, $q_update_29);
			$q_update_30="UPDATE users_authorization SET authorization='$rb_customer' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='CUSTOMER'";
            $exec_update_30=mysqli_query($db_connection, $q_update_30);
            $q_update_31="UPDATE users_authorization SET authorization='$rb_vendor' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='VENDOR'";
            $exec_update_31=mysqli_query($db_connection, $q_update_31);
			$q_update_32="UPDATE users_authorization SET authorization='$rb_soa_report' WHERE users_id='$id' AND branch_id='$branch_id' AND menu='SOA_REPORT'";
            $exec_update_32=mysqli_query($db_connection, $q_update_32);
			
            if ($exec_update_1 && $exec_update_2 && $exec_update_3 && $exec_update_4 && $exec_update_5 && $exec_update_51 && $exec_update_6 && $exec_update_7 && 
			    $exec_update_8 && $exec_update_9 && 
	            $exec_update_10 && $exec_update_11 && $exec_update_12 && $exec_update_13 && $exec_update_14 && $exec_update_15 && $exec_update_16 && $exec_update_17 && 
		        $exec_update_18 && $exec_update_19 && $exec_update_20 && $exec_update_21 && $exec_update_22 && $exec_update_23 && $exec_update_24 && $exec_update_25 && 
				$exec_update_26 && $exec_update_27 && $exec_update_28 && $exec_update_29 && $exec_update_30 && $exec_update_31 && $exec_update_32)
		       {
		         mysqli_commit($db_connection);
		         ?>
                    <script language="javascript">
				      opener.location.reload(true);
				      window.close();
			        </script>
		         <?php 
		       }
	        else
	          {
		        mysqli_query($db_connection, 'rollback')
		        ?>
	               <script language="javascript">
			         alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
	                 window.location.href='javascript:history.back(1)';
	               </script>
	            <?php  
		      }	
		 }    
     */
?>

<script language="javascript">
  var open_child=null; 
  function call_setting_branch(users_id)
           {
		     var w=625;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
		     var t=(screen.height/2)-(h/2); 
			 value_id=users_id;
			 open_child=window.open('../../data/users/branch_authorization.php?id='+value_id, 'f_set_branch_authorization', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0, scrollBars=Yes');
		   }
		  
  function select_unselect_all()
           {
		     var check_select=document.getElementsByName('cb_all');
		     var select_unselect_all_data = document.getElementsByName('cb_data[]');
			 
             for (i = 0; i<select_unselect_all_data.length; i++)
			     {
				   if (check_select[0].checked==true)
				      {
                        select_unselect_all_data[i].checked = true ;
					  }
				   else
				      {
					    select_unselect_all_data[i].checked = false ;
					  }	  	
				 }   
		   }

  function display_authorization(x)
           {
			 var id_user='<?php echo $id;?>';
		     var branch=x;
			 window.location='../../data/users/authorization_users.php?id='+id_user+'&b='+branch;
		   }
</script>