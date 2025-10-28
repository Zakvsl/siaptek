<style type="text/css">
  .tbl-background {
                    background-color: #FFFFFF;
                    background-image:url(../images/background/siaptek_image.jpg);
		            background-size:100% 100%;
					font-family:calibri;
                  }
  .table-list-header {
                       background-color: #FFFFFF; 
                     } 
</style>

<?php
 include "../library/check_session.php";
 include "../library/style.css";
 
 // Redirect to login page if not logged in and no page parameter
 if (!isset($_SESSION['ses_siaptek_admin']) && !isset($_GET['page'])) {
     header("Location: index.php?page=log-in");
     exit;
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INVENTORY</title>
</head>
<table border="0" width="100%" class="table-list-home">
       <tr>
	       <td width="40%" colspan="3">
               <table width="100%" border="0" cellspacing="2" cellpadding="2">
                      <tr>
							<td>
							    <table border="0" width="100%" bgcolor="#FFFFFF" class="table-list-header">
								       <tr>
									       <td width="5%">
										       <div align="left"><img src="../images/background/logoSiaptek.png" width="140px" height="35px"/></div>
									     </td>
	                                       <td width="70%" valign="top" align="">
										   <div style= "margin-left:10px"> 
										   <font size="<?php if (isset($_SESSION['ses_user_id'])) echo "+1"; else echo "+1";?>">
															   <b>SISTEM INFORMASI PEREDARAN ASET</b><br />
															   <b>PT. SURVEY INSPEKSI AUDIT PRATAMA TEKINDO</b><br />
															   <b>Jln. Minangkabau Barat No. 46 - Jakarta Selatan</b>
															   <b><?php 
		                                                               if (isset($_SESSION['ses_branch']))
		                                                                  { 
			                                                                $branch_selected=$_SESSION['ses_branch'];
			                                                                $q_check_branch="SELECT branch_id,branch_name FROM branch WHERE branch_id='$branch_selected'";
			                                                                $exec_check_branch=mysqli_query($db_connection, $q_check_branch);
			                                                                $field_data=mysqli_fetch_array($exec_check_branch);
                                                                            echo $field_data['branch_name']."<br>";
			                                                              }	    
		                                                          ?></b>
															</font>
										   </div>
										                    
									     </td> 
										   <td width="367" valign="top" align="right"><?php 
										                                                   if (isset($_SESSION['ses_siaptek_admin'])) 
										                                                      { 
																							    echo "<font color='#0054a6'><i>Welcome : ".$_SESSION['ses_user_naming']."</i><br>"; 
																							    echo "<a href='../index/index.php' style='text-decoration:none'>Home</a> | <a href='../data/log_in_out/log_out.php' style='text-decoration:none' title='Sign Out'>Sign Out</a></font>"; 
																							  }	
																					  ?>					
										   </td>
									   </tr>
								</table>
						    </td>  
                      </tr>
               </table>
		   </td>
	   </tr>
</table>
  
 <?php 
   if (!isset($_SESSION['ses_siaptek_admin']))
      {
	    ?>
		  <table width="100%">
		    <tr>
			    <td bgcolor="black" height="50px">
				</td>
			</tr>
		  </table>
		<?php
	  }
   else
        include "menu.php";

 ?>
<br>
  <table width="100%" <?php if (isset($_SESSION['ses_branch']) && !isset($_GET['page']))
                                echo "class='tbl-background'"; ?>>	
    <tr>
      <td valign="top"><?php include "open_page.php";?>
	                   <div align="center">
	                        <?php
							      if (isset($_SESSION['ses_branch']) && isset($_GET['page']))
									  echo "";
								  else
							      if (!isset($_GET['page']))
									  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
								  else
							      if ((!isset($_SESSION['ses_branch']) && !isset($_GET['page'])) || (isset($_GET['page'])=='log-in'))
			                           echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
                            ?>
	                   </div>
	  </td>
    </tr>
    <tr>
      <td>
	  <div align="center">
	    <?php 
	      if (isset($_GET['page'])!='log-in' && isset($_GET['page'])!='')
	          echo  "Copyright by adien.kom_soft"; 
        ?></div></td>
    </tr>
  </table>
<table width="100%" class="table-list-home">
       <tr>
	       <td>
               <p align="center" style="vertical-align:top">&copy; 2025 PT. SURVEY INSPEKSI AUDIT PRATAMA TEKINDO. All Rights Reserved.</p>
		   </td>
	  </tr>
</table>
<!---</form>--->
</body>
</html>
