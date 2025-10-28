<?php
 include "../../library/check_session.php";
 $project_id=$_SESSION['ses_project'];
 include "../../library/style.css";
 include "../../library/db_connection.php";
 $q_get_contract_no="SELECT prj_id, prj_code, prj_name FROM project WHERE prj_id='$project_id'";
 $exec_get_contract_no=mysqli_query($db_connection,$q_get_contract_no);
 $field_data=mysqli_fetch_array($exec_get_contract_no);
 if (mysqli_num_rows($exec_get_contract_no)==0)
    { 
      ?>
	      <script language="javascript">
		    alert('No Project is found!');
			window.close();
		  </script>
	  <?php
	}	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Upload Purchase Order</title>
</head>
<body>
<form action="" method="post" enctype="multipart/form-data" name="f_upload" id="f_upload">
  <table width="100%" border="0" cellspacing="2" cellpadding="2">
    <tr>
      <th colspan="3" scope="col">UPLOAD PURCHASE ORDER</th>
    </tr>
    <tr>
      <td width="10%" nowrap="nowrap" valign="top">Project Name </td>
      <td width="1%" valign="top">:</td>
      <td width="89%"><input type="hidden" id ="txt_id" name="txt_id" value="<?php echo $field_data['prj_id'];?>"/><?php echo $field_data['prj_name']." - [".$field_data['prj_code']."]";?></td>
    </tr>
    <tr>
      <td>File Name </td>
      <td>:</td>
      <td><label>
        <input type="file" name="txt_file" id="txt_file"/>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>
        <input type="button" name="btn_generate_template" id="btn_generate_template" value="Generate Template" onClick="call_generate_template()" />
        <input type="button" name="btn_upload" id="btn_upload" value="Upload" onClick="call_upload_file()"/>
        <input type="button" name="btn_close" id="btn_close" value="Close" onClick="window.close('<?php echo $project_id;?>')"/></td>
    </tr>
  </table>
</form>
</body>
</html>
  
 <script language="javascript">
   function call_upload_file(x)
            {
			  var x=document.getElementById('txt_file').value.trim();
			  if (x=='')
			      alert('No File Selected!');
			  else
			     {
				   f_upload.action='file_upload_execute.php?p='+x;
				   f_upload.submit(); 
				 }
			}  
   function call_generate_template()
            {
		      f_upload.action='generate_template.php';
			  f_upload.submit(); 
			}
 </script> 
