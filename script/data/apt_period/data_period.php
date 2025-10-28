<?php
 include "../library/check_session.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  
  $array_field=array("period", "year", "period_status", "closing_status");
  $array_sorting=array("asc", "desc");
  
  $row=96;	
  if (isset($_POST['s_page']))
     {
	   $paged=$_POST['s_page']; 
	   $first_data=(($paged-1)*$row);
	 }
  else
     {  
	   $paged=1;
	   $first_data=0;
	 }	  
  							
  $query_get_apt_period="SELECT aptp_id, aptp_month_1, aptp_month_2, aptp_year, aptp_month_2 AS aptp_period,
                                CASE aptp_is_active_period
                                     WHEN '0' THEN 'Aktif'
                                     WHEN '1' THEN 'Tidak Aktif'
                                END aptp_is_active_period,
                                CASE aptp_is_closed
                                     WHEN '0' THEN 'Belum ditutup'
                                     WHEN '1' THEN 'Sudah ditutup'
                                END aptp_is_active
                         FROM apt_period
						 ORDER BY aptp_year DESC, aptp_month_1 DESC
			             LIMIT $first_data,$row";
  $query_page="SELECT count(aptp_id) as total_page FROM apt_period"; 
  
  
  $q_get_last_year="SELECT MAX(aptp_year) AS last_year FROM apt_period";
  $exec_get_last_year=mysqli_query($db_connection, $q_get_last_year);
  $field_apt_period=mysqli_fetch_array($exec_get_last_year);
  //echo $query_get_apt_period;	 
  	
  $current_year=date('Y');	   
?>

<form id="f_period" name="f_period" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td scope="col"><div align="left">
        <h2>DAFTAR TRANSAKSI PERIOD</h2>
      </div></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td scope="col">Generate Transaksi Period Untuk Tahun :&nbsp;<select name="s_last_period" id="s_last_period">
	                                                                       <option value="0">--Pilih Tahun--</option>
	                                                            <?php
																     for ($i=$field_apt_period['last_year']+1; $i<=$current_year; $i++)
																	      echo "<option value='$i'>$i</option>"; 
																?>
                                                             </select>
	  <?php 
            if ($apt_period_autho=='D')
			   { 
                 echo "<input name='btn_generate' type='button' id='btn_generate' value='Generate' onclick='generate_apt_period()'/>";
			   }
	  ?>
	  </td>
      <td align="right"></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="3%" scope="col" class="th_ltb">No</th>
          <th width="15%" scope="col" class="th_ltb">Transaksi Period</th>
          <th width="20%" scope="col" class="th_ltb">Tahun</th>
          <th width="20%" scope="col" class="th_ltb">Status Period </th>
          <th width="20%" scope="col" class="th_ltbr">Status Closing </th>
		  <?php 
            if ($apt_period_autho=='D')
                echo "<th width='20%' scope='col' class='th_ltbr'>Aksi</th>";
		  ?> 
          </tr>
		  <?php
				$exec_query=mysqli_query($db_connection, $query_get_apt_period);
				$total_period=mysqli_num_rows($exec_query);
				$no=0;
				while ($data_period=mysqli_fetch_array($exec_query))
				      {
					    $no++;
						if ($data_period['aptp_is_active_period']=='Aktif' && $data_period['aptp_is_active']=='Belum ditutup')
						   { 
						     $font_1="<font color='#0000FF'>";
							 $font_2="</font>";
						   }
						else
						   {
						     $font_1="<font color='#000000'>";
							 $font_2="</font>";
						   }
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_period['aptp_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $font_1.$no.$font_2;?></td>
          <td class="td_lb"><?php echo $font_1.$data_period['aptp_period'].$font_2;?></td>
		  <td class="td_lb"><?php echo $font_1.$data_period['aptp_year'].$font_2;?></td>
          <td class="td_lb"><?php echo $font_1.$data_period['aptp_is_active_period'].$font_2;?></td>
          <td class="td_lbr"><?php echo $font_1.$data_period['aptp_is_active'].$font_2;?></td>
		  <?php
			  if ($apt_period_autho=='D')
				 {
				   echo "<td class='td_lbr'>";
			       if ($data_period['aptp_is_active_period']=='Aktif' && $data_period['aptp_is_active']=='Belum ditutup')
				      echo "<input type='button' id='btn_close_period' name='btn_close_period' value='Tutup Period' onclick='close_period(".$data_period['aptp_id'].")'/>";
			       echo "</td>";
				 }
			  ?>
          </tr><?php } ?>
      </table></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $query_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td align="right">Halaman ke: 
		                                                                               <select name="s_page" id="s_page" onchange="submit()">
																								 <?php
																								      $pages=array();
																								      if ($maks_rows==0)
																								         {
																									       $maks_rows=1;
																									     }
                                                                                        		      $i=0;
                                                                                     			      while ($i<$maks_rows)
			                                                                                                {
		                                                                                    		          $i++;
																										      $pages[]=$i;
			                                                                                         	     }
																									  if (isset($_POST['s_page']))
																									     {
																										   foreach ($pages as $page)
																										           {
																												     if ($_POST['s_page']==$page)
																													    {
																														  $selected_page="selected";
																														}
																												     else
																													    {
																														  $selected_page="";
																														}	
																													 ?>
																													    <option value="<?php echo $page;?>" <?php echo $selected_page;?>><?php echo $page;?></option>
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }
																									  else
																									     {
																										   foreach ($pages as $page)
																										           {
																													 ?>
																													    <option value="<?php echo $page;?>"><?php echo $page;?></option>  
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }	 		 
																								 ?> 
	                                                                         </select>
		                                                                               dari
		                                                                               <?php  echo $maks_rows; ?></td>  
		</td>  
    </tr>
  </table>
</form>

<script language="javascript">
    var open_child=null;
   
    function select_unselect_all(x)
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('check_data[]');
			 
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
	  
	function close_period(x)
	         {
			   var answer=confirm('Apakah yakin akan menutup aktif period transaksi?');
			   if (answer)
			      {
			        f_period.action='../data/apt_period/close_period.php?p='+x;
			        f_period.submit(); 
				  }
			 }	
			 
    function generate_apt_period() 
	         {
			   year=document.getElementById('s_last_period').value;
			   if (year=='0')
			      {
				    alert('Silahkan pilih Transaksi Period!');
				  }
			   else
			      {
			        f_period.action='../data/apt_period/generate_period.php';
			        f_period.submit(); 
			      }
			 }
			 
</script>

</body>
</html>
