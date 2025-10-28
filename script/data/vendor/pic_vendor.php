<?php
  include "../../library/check_session.php";
  include "../../library/style.css";
  include "../../library/db_connection.php";
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_customer="SELECT cust_id, cust_code, cust_name, cust_npwp, cust_address, 
	                    CASE cust_status
						     WHEN '0' THEN 'Active'
							 WHEN '1' THEN 'InActive'
						END cust_status, 
						cust_phone, cust_fax, cust_web_address, cust_email 
                        FROM customer
				        WHERE cust_id='$id'";
	//   echo $q_get_customer;
	   $exec_get_customer=mysqli_query($db_connection, $q_get_customer);
	   $total_customer=mysqli_num_rows($exec_get_customer);
	   $field_customer=mysqli_fetch_array($exec_get_customer);
	   if ($total_customer==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Customer tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $cust_id=$field_customer['cust_id'];
		    $cust_code=$field_customer['cust_code'];
			$cust_name=$field_customer['cust_name'];
			$cust_npwp=$field_customer['cust_npwp']; 
			$cust_address=$field_customer['cust_address'];
			$cust_status=$field_customer['cust_status'];
			$cust_phone=$field_customer['cust_phone'];
			$cust_fax=$field_customer['cust_fax'];
			$cust_web=$field_customer['cust_web_address'];
			$cust_email=$field_customer['cust_email'];
		  }	 
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_pic_vendor" id="f_pic_vendor">
  <table width="892" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="6" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left">NAMA PIC VENDOR</th>
    </tr>
    <tr>
      <td width="18%">Kode Vendor </td>
      <td width="1%">:</td>
      <td width="36%"><?php echo $cust_code;?></td>
      <td width="15%">No Telpon</td>
      <td width="1%">:</td>
      <td width="29%"><label><?php echo $cust_phone;?></label></td>
    </tr>
    <tr>
      <td>Nama Vendor</td>
      <td>:</td>
      <td><?php echo $cust_name;?></td>
      <td>Fax No</td>
      <td>:</td>
      <td><?php echo $cust_fax;?></td>
    </tr>
    
    <tr>
      <td valign="top">NPWP No</td>
      <td valign="top">:</td>
      <td><?php echo $cust_npwp;?></td>
      <td>Alamat Web</td>
      <td>:</td>
      <td><?php echo $cust_web;?></td>
    </tr>
    <tr>
      <td valign="top">Alamat</td>
      <td valign="top">:</td>
      <td><?php echo $cust_address;?></td>
      <td>Alamat Email</td>
      <td>:</td>
      <td><label><?php echo $cust_email;?></label></td>
    </tr>
    
    
    <tr>
      <td>Status</td>
      <td>:</td>
      <td><?php echo $cust_status;?>      
      <td>      
      <td>      
    <td></tr>    
    <tr>
      <td>
	    <?php
		   if ($vendor_autho=='W' || $vendor_autho=='D')
		      {
			    if ($vendor_autho=='W')
				    echo "<input type='button' id='btn_add' name='btn_add' value='+' onClick='input_data()'>";
				else
				if ($vendor_autho=='D')
				   {
				     echo "<input type='button' id='btn_add' name='btn_add' value='+' onClick='input_data()'>";
		             echo "<input type='button' id='btn_update' name='btn_update' value='+/-' onClick='update_data()'>";
		             echo "<input type='button' id='btn_delete' name='btn_delete' value='-' onClick='delete_data()'>";
				   } 
			  }
		?>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
          <tr>
            <th width="4%"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
            <th width="4%">No</th>
            <th width="32%">Nama</th>
            <th width="19%">Tlp</th>
            <th width="21%">Email</th>
            <th width="24%">Jabatan</th>
          </tr>
		  <?php
		    $q_get_pic="SELECT custp_id, custp_name, custp_phone, custp_email, custp_position_name
                        FROM customer_pic
                        WHERE cust_id='$id'
						ORDER BY custp_name ASC";
			$exec_get_pic=mysqli_query($db_connection, $q_get_pic);
			if (mysqli_num_rows($exec_get_pic)>0)
			   {
			     $no=1;
			     while ($field_pic=mysqli_fetch_array($exec_get_pic))
				       {
			             echo "<tr>";
						 echo "<td><input type='checkbox'  id ='check_data[]' name='check_data[]' value='".$field_pic['custp_id']."'/></td>";
				         echo "<td>".$no++."</td>";
                         echo "<td>".$field_pic['custp_name']."</td>";
                         echo "<td>".$field_pic['custp_phone']."</td>";
                         echo "<td>".$field_pic['custp_email']."</td>";
                         echo "<td>".$field_pic['custp_position_name']."</td>";
                         echo "</tr>";
					   } 
			   }
		  ?>
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
	
		   
	function input_data()
	         {
			   var w=390;
			   var h=200;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   var cid='<?php echo $id;?>';
			   open_child=window.open("../../data/vendor/cru_pic_vendor.php?c=i&cid="+cid, "f_cru_pic_vendor", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }	
			 
	function disable_parent_window()  // akan dipanggil di tag <body>
	         {
			   if (open_child && !open_child.closed) 
			      {
                    open_child.focus();
				  }	
			 }		    
	 
	function update_data()
	         {
			   var w=390;
			   var h=200;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   var x=document.getElementsByName('check_data[]').length;
			   var y=document.getElementsByName('check_data[]');
			   var z=0;
			   var id='';
			   var cid='<?php echo $id;?>';
               for (i=0; i<x; i++)
			       {
				     if (y[i].checked==true)
				        {
					      z++;   
						  if (z==1)
						     {
						       var value_id=[y[i].value];	
						     }
						  else
						     {
					           value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						     }       
					    }   
				   } 	
				 
			   if (z==0)
			      {
				    alert('Tidak ada data yang dipilih!');
				  }
			   else
			   if (z>1)
			      {
				    alert('Silahkan pilih salah satu data!');  
				  }	  
		       else
			      {
				    open_child=window.open('../../data/vendor/cru_pic_vendor.php?c=u&id='+value_id+'&cid='+cid, 'f_cru_pic_vendor', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  }
			 }  
			 
  function delete_data()
           {
		     var a='<?php echo $id;?>';
		     var x=document.getElementsByName('check_data[]').length;
			 var y=document.getElementsByName('check_data[]');
			 var z=0;
			 var id='';
             for (i=0; i<x; i++)
			     {
				   if (y[i].checked==true)
				      {
					    z++;   
						if (z==1)
						   {
						     var value_id=[y[i].value];	
						   }
						else
						   {
					         value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						   }       
					  }   
				 } 	
				 
			 if (z==0)
			    {
				  alert('Tidak ada data yang dipilih!');
				}
		     else
			    {
				  var answer=confirm('Apakah yakin akan menghapus data terpilih?');
				  if (answer)
				     {
					   document.f_pic_vendor.action="../../data/vendor/delete_pic_vendor.php?cid="+a;
					   document.f_pic_vendor.submit();
					 }  
				}
		   }   
</script>






