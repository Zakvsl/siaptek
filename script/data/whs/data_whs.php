<?php
  include "../library/check_session.php";
  $branch_id=$_SESSION['ses_id_branch'];
  include "../library/db_connection.php";
  
  echo "<b>---- DAFTAR LOKASI GUDANG ----</b><br><br>";
  $q_show_whs="SELECT branch_id AS branch_id, '' AS whsl_id,'' AS branch_id_1, branch_name,'' AS whsl_path,'' AS whsl_code,'' AS whsl_name,'' AS whsl_level
               FROM branch
			   WHERE branch_id='$branch_id' 
               UNION                    
               SELECT '' AS branch_id, CAST(whsl_id AS CHAR) AS whsl_id, branch.branch_id, branch_name, CAST(whsl_id AS CHAR) 
			          AS whsl_path, whsl_code, whsl_name, whsl_level
               FROM warehouse_location
               LEFT JOIN branch ON warehouse_location.branch_id=branch.branch_id
               WHERE whsl_level='1' AND warehouse_location.branch_id='$branch_id'
               UNION
               SELECT '' AS branch_id, CAST(whsl_id AS CHAR) AS whsl_id, branch.branch_id, branch_name, whsl_parent_path, whsl_code, whsl_name, whsl_level
               FROM warehouse_location
               LEFT JOIN branch ON warehouse_location.branch_id=branch.branch_id
               WHERE whsl_level!='1' AND warehouse_location.branch_id='$branch_id'
               ORDER BY branch_name, whsl_path, whsl_name"; 
  $exec_whs=mysqli_query($db_connection, $q_show_whs);
  if (mysqli_num_rows($exec_whs)==0)
     {
	   echo "TIDAK ADA LOKASI GUDANG";
	 }
  else
     { 	 
	   $branch_name='';
	   $level_1="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	   $level_2="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	   $level_3="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	   $level_4="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	   $level_5="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "<form id='f_whs' name='f_whs' method='post' enctype='multipart/form-data'>";	
	   while ($field_whs=mysqli_fetch_array($exec_whs))
	         {
			   $branch_id=$field_whs['branch_id'];
			   $whsl_id=$field_whs['whsl_id'];
			   $whsl_name=$field_whs['whsl_name'];
			   $whsl_code=$field_whs['whsl_code'];
			   $whsl_level=$field_whs['whsl_level'];
			   if ($branch_name!=$field_whs['branch_name'])
			      {
				    if ($whs_autho=='W' || $whs_autho=='D')
			            echo "<b><font color='#FF0000'>".$field_whs['branch_name']."</font> <a href='javascript:void(1)' onclick=input_data('".$branch_id."','".$whsl_id."') style='text-decoration:none'>[+]</a></b><br>";
					else
					    echo "<b>".$field_whs["branch_name"]."</b><br>";
				  }	 
			   else	  
			   if ($whsl_level=='1')
			      {                       
				    ?>
			           <b><a href="javascript:void(1)" onclick="display_stock('<?php echo $whsl_id;?>')" style="text-decoration:none"><font color='#000000'><?php echo $level_1.$whsl_name;?> - [<?php echo $whsl_code;?>]</font></a></b>&nbsp;&nbsp;&nbsp;
					      <?php 
						     if ($whs_autho=='W')
							    {
							     ?>
				                    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
								 <?php
								}
							 else	
							 if ($whs_autho=='D')
							    {
							     ?>
								    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="update_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+/-]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="delete_data('<?php echo $whsl_id;?>')" style="text-decoration:none">[-]</a>
				   <?php 	    } echo "<br>";		 
				   
				 }			
			   else	  
			   if ($whsl_level=='2')
			       {
				     ?>
			           <b><a href="javascript:void(1)" onclick="display_stock('<?php echo $whsl_id;?>')" style="text-decoration:none"><font color='#000099'><?php echo $level_2.$whsl_name;?> - [<?php echo $whsl_code;?>]</font></a></b>&nbsp;&nbsp;&nbsp;
				          <?php 
						     if ($whs_autho=='W')
							    {
							     ?>
				                    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
								 <?php
								}
							 else	
							 if ($whs_autho=='D')
							    {
							     ?>
								    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="update_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+/-]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="delete_data('<?php echo $whsl_id;?>')" style="text-decoration:none">[-]</a>
				   <?php 	    } echo "<br>";				 
				 }
			   else
			   if ($whsl_level=='3')   
			       { 
				     ?>
			           <b><a href="javascript:void(1)" onclick="display_stock('<?php echo $whsl_id;?>')" style="text-decoration:none"><font color='#0000CC'><?php echo $level_3.$whsl_name;?> - [<?php echo $whsl_code;?>]</font></a></b>&nbsp;&nbsp;&nbsp;
				          <?php 
						     if ($whs_autho=='W')
							    {
							     ?>
				                    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
								 <?php
								}
							 else	
							 if ($whs_autho=='D')
							    {
							     ?>
								    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="update_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+/-]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="delete_data('<?php echo $whsl_id;?>')" style="text-decoration:none">[-]</a>
				   <?php 	    } echo "<br>";				 
				 }
			   else
			   if ($whsl_level=='4') 
			       {
				     ?>
			           <b><a href="javascript:void(1)" onclick="display_stock('<?php echo $whsl_id;?>')" style="text-decoration:none"><font color='#0000FF'><?php echo $level_4.$whsl_name;?> - [<?php echo $whsl_code;?>]</font></a></b>&nbsp;&nbsp;&nbsp;
				          <?php 
						     if ($whs_autho=='W')
							    {
							     ?>
				                    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
								 <?php
								}
							 else	
							 if ($whs_autho=='D')
							    {
							     ?>
								    <a href="javascript:void(1)" onclick="input_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="update_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+/-]</a>&nbsp;
							        <a href="javascript:void(1)" onclick="delete_data('<?php echo $whsl_id;?>')" style="text-decoration:none">[-]</a>
				   <?php 	    } echo "<br>";			 
				 }
			   else
			   if ($whsl_level=='5')
			      {
				    ?>
			        <a href="javascript:void(1)" onclick="display_stock('<?php echo $whsl_id;?>')" style="text-decoration:none"><font color='#0099FF'><?php echo $level_5.$whsl_name." - [".$whsl_code."]";?></font></a>&nbsp;&nbsp;&nbsp;
					    <?php 
						     if ($whs_autho=='D')
							    {
							     ?>
				                    <a href="javascript:void(1)" onclick="update_data('<?php echo $branch_id."','".$whsl_id;?>')" style="text-decoration:none">[+/-]</a>&nbsp;
					                <a href="javascript:void(1)" onclick="delete_data('<?php echo $whsl_id;?>')" style="text-decoration:none">[-]</a>
					   <?php	} echo "<br>";	
				  }		
			   $branch_name=$field_whs['branch_name'];
			 }
	   echo "</form>";
	 }				
?> 

<script language="javascript">
  	function input_data(branch_id, whsl_id)
	         {
			   var w=420;
			   var h=200;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/whs/cru_whs.php?c=i&brid="+branch_id+"&whid="+whsl_id, "f_cru_whs", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }
			 
  	function update_data(branch_id, whsl_id)
	         {
			   var w=420;
			   var h=200;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/whs/cru_whs.php?c=u&brid="+branch_id+"&whid="+whsl_id, "f_cru_whs", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }
			 
	function delete_data(x)
	         {
			   var message=confirm('Apakah yakin akan menghapus data terpilih?')
			   if (message)
			      {
				    alert('x='+x);
				    document.f_whs.action='../data/whs/delete_whs.php?id='+x;
				    document.f_whs.submit();
				  }   
			 }			 
	function display_stock(x)
	         {
			   var w=1500;
			   var h=650;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/whs/stock_tube.php?id="+x, "f_stock_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0, scrollbars=Yes"); 
			 }
</script>
