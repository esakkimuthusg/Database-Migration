<?php
if (!isset($_SESSION)) session_start();	
include_once("classes/DbConfig.php");
include_once("classes/DbActions.php");

// get database connection
$dbConfig = new DbConfig();
$db1Connection = $dbConfig->db1Connection();
$db2Connection = $dbConfig->db2Connection();

$dbActions = new DbActions();
$tablesResult = $dbActions->getTables($db1Connection);	
$tablesResult2 = $dbActions->getTables($db2Connection);	
?>
<html>
<head>
<title>Database Migration</title>	
</head>

<body>
<h2 align="center">Database Migration</h2>
<?php
if(isset($_POST['dbMigration']) && ($_POST['dbMigration']==1))
{
	$customLogFile = CUSTOM_LOG_PATH.$db2Connection[1].".txt";
	file_put_contents($customLogFile,'');
	$dbResult = $dbActions->exportDb($db1Connection,$tables=false,$backup_name=false );
	if($GLOBALS['http_response_code']==200)
	{
		 $dropTablesResult = $dbActions->dropTables($db2Connection);
		 if($GLOBALS['http_response_code']==200){
			$dbResult2 = $dbActions->importDb($db1Connection,$db2Connection,$tables=false,$backup_name=false );
			echo "<h3 align='center'>Backup taken and Migration completed successfully<h3>";
			echo "<a href='".$dbResult."' align='center'>Download Backup</a><br>";
			echo "<a href='".$dbResult2."'align='center'>Download Log</a><br><br>";
		 }	 
	}
	else
	{
		echo "<h3 align='center'>Oops something went wrong. Please try again later.</h3>";
	}
}
?>
<table width="100%" border="1" align="center">
	<td><table width="50%" border="0" align="center">
	<tr> 
			<td colspan="2"><h3>Source - <?php echo $db1Connection[1]; ?> Database Tables</h3></td>
		</tr>			
	<?php if(!empty($tablesResult) && $tablesResult['status']="fail"){ ?>
		<tr> 
			<td align="center"><strong><?php if(!empty($tablesResult)) { echo $tablesResult['message']; } ?></strong></td>
		</tr>
	<?php } if(!empty($tablesResult) && $tablesResult['status']="success"){ 
		foreach ($tablesResult['data'] as $key => $res) {  ?>
		<tr> 
			<td><?php echo $res['Tables_in_'.$db1Connection[1].'']; ?></td>
		</tr>
	<?php } } ?>
			
	</table></td>
	<td><table width="50%" border="0" align="center">
	
	<tr> 
		<td colspan="2"><h3>Destination - <?php echo $db2Connection[1]; ?> Database Tables</h3></td>
	</tr>	
	<?php if(!empty($tablesResult2) && $tablesResult2['status']="fail"){ ?>
		<tr> 
			<td align="center"><strong><?php if(!empty($tablesResult2)) { echo $tablesResult2['message']; } ?></strong></td>
		</tr>
	<?php } if(!empty($tablesResult2) && $tablesResult2['status']="success"){ 
		foreach ($tablesResult2['data'] as $key => $res) {  ?>
		<tr> 
			<td><?php echo $res['Tables_in_'.$db2Connection[1].'']; ?></td>
		</tr>
	<?php } } ?>
			
	</table></td>
</table>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" name="dbMigrationForm" id="dbMigrationForm" autocomplete="off">
<table width="50%" border="0" align="center">
            
	<tr>
	<td align="center" height="50"></td>
	</tr>	
	
   <tr>
	<td align="center"><input type="submit" name="dbMigration" id="dbMigration" value="Start">
	<input type="hidden" name="dbMigration" id="dbMigration" value="1"></td>
	</tr>		
			
</table>
</form>



</body>
</html>