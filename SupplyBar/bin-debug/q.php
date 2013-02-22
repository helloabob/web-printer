<?php
header("Content-type: text/html; charset=utf-8");
?>
<div>
<h2>防伪查询-Meritor</h2>
</div>
<form name="form1" method="post">
<input type="text" name="txtpar" width="160" style="width:160;" />
<br/>
<input type="text" name="txtchd" width="160" style="width:160;"/>
<br/>
<input type="submit" value="查询" width="160" style=" width:160;" />
</form>

<?php
if(isset($_REQUEST['txtpar'])&&isset($_REQUEST['txtchd'])){
	$url=substr($_SERVER['HTTP_REFERER'],0,strlen($_SERVER['HTTP_REFERER'])-5)."/php/server.php?userid=a&pwd=d&table=auddata&action=c&p=".$_REQUEST['txtpar']."&c=".$_REQUEST['txtchd'];
	$result=file_get_contents($url);
	if($result=="no"){
		?>
		<table border="1">
        <tr><td>追溯校验：不匹配</td></tr>
		</table>
        <?php
	}else{
		$xml=simplexml_load_string($result);
		?>
        <table border="1">
	<?php if($xml->type=="1"){ ?>
        <tr><td>追溯校验：匹配</td></tr>
	<?php
	}else{
	?>
	<tr><td>追溯校验：不匹配</td></tr>
	<?php
	}
 	?>
        <tr><td><?php echo "父零件：".$xml->N01; ?></td></tr>
        <tr><td><?php echo "描述：".$xml->N02; ?></td></tr>
        <tr><td><?php echo "批号：".$xml->N06; ?></td></tr>
	<tr><td><?php echo "单位：".$xml->N03; ?></td></tr>
        <tr><td><?php echo "版本：".$xml->N08; ?></td></tr>
        <tr><td><?php echo "子零件：".$xml->N09; ?></td></tr>
        <tr><td><?php echo "描述：".$xml->N10; ?></td></tr>
        <tr><td><?php echo "批号：".$xml->N14; ?></td></tr>
	<tr><td><?php echo "单位：".$xml->N11; ?></td></tr>
        <tr><td><?php echo "版本：".$xml->N16; ?></td></tr>
        </table>
		<?php
        
		//echo "<br/>";
		
		//echo "父零件描述：".$xml->N02."<br/>";
		//echo "父版本：".$xml->N08."<br/>";
		//echo "子零件：".$xml->N09."<br/>";
		//echo "子零件描述：".$xml->N10."<br/>";
		//echo "子版本：".$xml->N16."<br/>";
	}
}


?>