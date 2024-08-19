<?php
/*
Plugin Name: MasticExternalUrl
Plugin URI: https://github.com/kodaje
Description: Sebuah plugin untuk kemasukan url sumber luaran powerbi
Version: 1.0.1
Author: kodaje
Author URI: https://github.com/kodaje
License: GPL2
*/
register_activation_hook(__FILE__, 'crudOperationsTable');
  global $wpdb;
  $table_name = $wpdb->prefix . 'masticexturl';
  $actionLink = "mastic-ext-url%2Fcrud.php"; 

function crudOperationsTable() {
  global $wpdb;
  global $table_name;
  $table_name = $wpdb->prefix . 'masticexturl';

  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `reportid` text DEFAULT NULL,
  PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  ";
  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
  }
}

add_action('admin_menu', 'addAdminPageContent');

function addAdminPageContent() {
  add_menu_page('Mastic Ext Url', 'Mastic External Url', 'manage_options' ,__FILE__, 'crudAdminPage', 'dashicons-text');
}

function crudAdminPage() {
  global $wpdb;
  global $actionLink;
  global $table_name;

  if (isset($_POST['newsubmit'])) {
	$name = $_POST['description'];
	//$urlframe = $_POST['url'];
	
	$reportid = $_POST['reportid'];
	$reportchannel = $_POST['reportchannel'];

	$sql ="INSERT INTO $table_name(description,reportid,reportchannel) VALUES('$name','$reportid','$reportchannel')";
	 $wpdb->query($sql);
	  echo "<script>location.replace('admin.php?page=".$actionLink."');</script>";
  }

  if (isset($_POST['uptsubmit'])) {
	$id = $_POST['uptid'];
	$name = $_POST['uptname'];
	$reportid = $_POST['uptreportid'];
	$reportchannel = $_POST['uptreportchannel'];

	
	$wpdb->query("UPDATE $table_name SET description='$name',reportid='$reportid',reportchannel ='$reportchannel' WHERE id='$id'");
	echo "<script>location.replace('admin.php?page=".$actionLink."');</script>";
  }

  if (isset($_GET['del'])) {
	$del_id = $_GET['del'];
	$wpdb->query("DELETE FROM $table_name WHERE id='$del_id'");
	echo "<script>location.replace('admin.php?page=".$actionLink."');</script>";
  }
  
  ?>
  <div class="wrap">
	<h2>Senarai Url</h2>
	<table class="wp-list-table widefat striped">
	  <thead>
		<tr>
		  <th width="5%">ID</th>
		  <th width="5%">Jenis</th>
		  <th width="30%">Keterangan</th>
		  <!-- <th width="5%">Url</th> -->
		  <th width="35%">Reportid / Lain-lain</th>
		  <th width="25%">Pilihan</th>
		</tr>
	  </thead>
	  <tbody>
		<form action="" method="POST">
		  <tr>
			<td><input type="text" value="AUTO" disabled></td>
			<td><select name="reportchannel" required>
				<option value="0">Reference Reportid (Powerbi)</option>
				<option value="1">Lain-lain</option>
				</select> 
			</td>

			<td><input type="text" name="description" required></td>
			<!-- <td><textarea id="url" name="url" required></textarea></td> -->
			<td><textarea id="reportid" name="reportid" required></textarea></td>

			<td><button id="newsubmit" name="newsubmit" type="submit">Tambah</button></td>
		  </tr>
		</form>
		<?php
		  $sql ="SELECT * FROM ".$table_name;
		  $result = $wpdb->get_results($sql);
		  foreach ($result as $print) {
				$id=$print->id;
				$reportchannel=$print->reportchannel;
				if($reportchannel==0){
					$reportchannellbl = "Reference Powerbi";
				}else{
					$reportchannellbl="Lain-lain";
					
				}
			echo "
			  <tr>
				<td width='5%'>$id</td>
				<td width='5%'>$reportchannellbl</td>
				<td width='30%'>$print->description</td>
				<td width='35%'>";
				
				echo $print->reportid."<br>";
				?>
				<button onclick="salinText('<?php echo $id;?>')">Salin</button>
				
				<?php
				echo "</td>
				<td width='25%'><a href='admin.php?page=".$actionLink."&upt=$print->id'>Kemaskini</a> 
				&nbsp;&nbsp;&nbsp;";
				
				?>
 				
		<a onclick="return confirm('Anda pasti untuk hapus?');" title="Hapus rekod" href="admin.php?page=<?php echo $actionLink?>&del=<?php echo $print->id;?>">Hapus</a>	
					
					
				
				  
 				
		<?php
 			 echo "</td></tr>
			";
		  }
		?>
	  </tbody>  
	</table>
	<br>
	<br>
	<?php
	  if (isset($_GET['upt'])) {
		$upt_id = $_GET['upt'];
		$sql ="SELECT * FROM ".$table_name." WHERE id='$upt_id'";
		$result = $wpdb->get_results($sql);
		foreach($result as $print) {
		  $name = $print->description;
		  $reportid = $print->reportid;
		  $reportchannel= $print->reportchannel;
		}
		echo "
		<table class='wp-list-table widefat striped'>
		  <thead>
			<tr>
			  <th width='10%'>ID</th>
			  <th width='15%'>Jenis</th>
			  <th width='45%'>Keterangan</th>
			  <th width='5%'>Reportid</th>
			  <th width='25%'>Pilihan</th>
			</tr>
		  </thead>
		  <tbody>
			<form action='' method='post'>
			  <tr>
				<td width='25%'>$print->id <input type='hidden' id='uptid' name='uptid' value='$print->id'></td>
 				<td width='25%'><select name='uptreportchannel' required>";?>
				 
					<option value='0' <?php if($reportchannel==0){ echo "selected";}?>>Reference Reportid (Powerbi)</option>
					<option value='1' <?php if($reportchannel==1){ echo "selected";}?>>Lain-lain</option>
					
					
					
				<?php echo "</select> 
				</td>
				<td width='25%'><input type='text' name='uptname' value='$print->description' required></td>
				<td width='25%'>
				<textarea name='uptreportid' required>".$print->reportid."</textarea></td>
				<td width='25%'><button id='uptsubmit' name='uptsubmit' type='submit'>Simpan</button> <a href='admin.php?page=".$actionLink."'><button type='button'>Batal</button></a></td>
			  </tr>
			</form>
		  </tbody>
		</table>";
	  }
	?>
	
<input style="display:none;" type="text" value="https://localhost/portal/?id=" id="myInput">
  </div>
  
  
<script>  
function salinText(id){		  
	var copyText = document.getElementById("myInput");
	copyText.select();
	copyText.setSelectionRange(0, 99999); // For mobile devices
	var str="[reportpowerbi id='"+id+"'][/reportpowerbi]";
	
   	navigator.clipboard.writeText(str);
  	alert('Telah disalin\n' + str );	  
}
</script>



  
  
  <?php
   
}

  

function report_link_att($atts, $content = null) {
	
	$default = array(
		'id' => $id,
	);
	$a = shortcode_atts($default, $atts);
	$id=$a['id'];
	$content = do_shortcode($content);

	//===================================//
	
 	global $wpdb;
	global $table_name;
	$table_name = $wpdb->prefix . 'masticexturl';
		
	$sql="SELECT * FROM ".$table_name." WHERE id='$id' LIMIT 1";
	$query = $wpdb->prepare($sql, $prize_bond_id);
	
	$row = $wpdb->get_row($query);
	$reportchannel=$row->reportchannel;//0-iframe,1-unique
	$_reportid = $row->reportid;
	$url =$row->url;

	$reportchannel = $row->reportchannel;

	$isExist=$wpdb->num_rows;
	if($isExist<1){
		return "Report not found.Please check/update Report ID";
	}
	
	
	//JIKA reportchannel = 0
	if($reportchannel==1){
		
		$output='';
		$output.='
		<style>
		#embedContainer {
		height: 960px;
		width : 100%;
		max-width: 100% !important;
		}
		 </style>';
		
		$output.='<iframe id="embedContainer" src="'.$_reportid.'" ></iframe>';
		
	return $output;
	exit;
	}
	
	
	
	//CONFIG 
	$workspaceId="xxxx";
	$clientId = "xxxx";
	$clientSecret ="xxxx";	
	$tenantId = 'xxxx';
	
	
	
	//DYNAMIC
     $reportId=$_reportid;
	
	
	
	$scope = 'https://analysis.windows.net/powerbi/api/.default';
	$authorityUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";
	$urlAccessToken = "https://login.windows.net/$tenantId/oauth2/token";
	$resource = 'https://analysis.windows.net/powerbi/api/.default';
	$ch = curl_init();
 	curl_setopt($ch, CURLOPT_URL, "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array(
	'scope' => "$scope",
	'client_id' => $clientId,
	'client_secret' => $clientSecret,
	'grant_type' => 'client_credentials'
	));
	$data = curl_exec($ch);
	curl_close($ch);

 	$data_obj = json_decode($data);
 	$access_token = $data_obj->{"access_token"};
	//echo $access_token;
	
	
	$headers = array(
	"Content-Type: application/json; charset=utf-8",
	"Authorization: Bearer $access_token"
	);
	
	
	$url = "https://api.powerbi.com/v1.0/myorg/groups/$workspaceId/reports/$reportId/GenerateToken";
	
 	$post_params = array(
	'accessLevel' => 'View',
	'allowSaveAs' => 'false'
	);
	
	$payload = json_encode($post_params);
	
	$ch2 = curl_init( $url );
	curl_setopt( $ch2, CURLOPT_POST, true);
	curl_setopt( $ch2, CURLINFO_HEADER_OUT, true);
	curl_setopt( $ch2, CURLOPT_POSTFIELDS, $payload);
	curl_setopt( $ch2, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch2, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec( $ch2 );
	
  	$data_obj2 = json_decode($response);
	$accesstoken = $data_obj2->{"token"};
	 
	if(!$accesstoken){
		return "Report ID not found, please check at powerbi system";
		exit;
	}
 	$embedUrl = "https://app.powerbi.com/reportEmbed?reportId=$reportId";
	 
	 
	
	$output = '';
	$res_path = plugin_dir_url( __FILE__ );
 	$output.='<script src="'.$res_path.'res/jquery.min.js"></script>';
 	$output.='<script src="'.$res_path.'res/powerbi.min.js"></script>';
 
	$output.='
	<style>
  	#embedContainer {
 	height: 960px;
	width : 100%;
    max-width: 100% !important;
	}
 	</style>';
	

	$output.= '<meta name="viewport" content="width=device-width, initial-scale=1.0">'; 
	
	$output.= '<div id="embedContainer"></div>';

	
	$output.='
	<script>
 		var accessToken = "'.$accesstoken.'";
 		var embedUrl = "'.$embedUrl.'";
 		var embedReportId = "'.$reportId.'";
 		var models = window["powerbi-client"].models;
 		var config = {
			type: "report",
			tokenType: models.TokenType.Embed,
			accessToken: accessToken,
			embedUrl: embedUrl,
			id: embedReportId,
			permissions: models.Permissions.All,
			settings: {
				filterPaneEnabled: true,
				navContentPaneEnabled: true
			}
		};
 	
			$("#RLS").prop("checked", true);
			$("#RLSdiv").show();
	
			$("#noRLSdiv").show();
		
	
	 const embedContainer = $("#embedContainer")[0];
		// Embed the report and display it within the div container.
		var report = powerbi.embed(embedContainer, config);
	</script>
 	';

	
	
	return $output;



}

add_shortcode('reportpowerbi', 'report_link_att');


