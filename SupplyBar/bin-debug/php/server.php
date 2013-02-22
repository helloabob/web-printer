<?php
//$action=$_REQUEST['action'];
// header("content-type:text/html; charset=utf-8"); //设置编码
header("Content-type: text/html; charset=utf-8");
//$xml=simplexml_load_file("http://test.smgbb.cn/server/data/010001-121103.xml");
error_reporting(2047);
$table=isset($_REQUEST['table'])?$_REQUEST['table']:"";
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
$userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:"";
$pwd=isset($_REQUEST['pwd'])?$_REQUEST['pwd']:"";

if($table=="user"){	
	if($action=="login"){
		if($tmp=getUser($userid,$pwd)){
			header("Content-type: text/xml; charset=utf-8");
			echo $tmp->asXML();
		}
		else "error";
	}else if($action=="modifypwd"){
		$newpwd=isset($_REQUEST["newpwd"])?$_REQUEST["newpwd"]:"";
		$tmp=getUser($userid,$pwd);
		if($tmp&&$newpwd!=""){
			$tmp->pwd=$newpwd;
			$xml->asXML("users.xml");
			echo "ok";
		}
	}else if($action=="modifyuser"){
		$newuser=isset($_REQUEST["newuser"])?$_REQUEST["newuser"]:"";
		$newpwd=isset($_REQUEST["newpwd"])?$_REQUEST["newpwd"]:"";
		$newdesc=isset($_REQUEST["newdesc"])?$_REQUEST["newdesc"]:"";
		$newrole=isset($_REQUEST["newrole"])?$_REQUEST["newrole"]:"";
		$newsupply=isset($_REQUEST["newsupply"])?$_REQUEST["newsupply"]:"";
		$tmp=getUser($userid,$pwd);
		if($tmp&&$tmp->role=="1"){
			$tmp_user=null;
			foreach($xml->user as $users){
				if($users->id==$newuser){
					$tmp_user=$users;
					break;
				}
			}
			if($tmp_user){
				$tmp_user->pwd=$newpwd;
				$tmp_user->desc=$newdesc;
				$tmp_user->role=$newrole;
				$tmp_user->supply=$newsupply;
				$xml->asXML("users.xml");
				echo "ok";
			}else{
				$xml->addChild("user","");
				$xml->user[count($xml->user)-1]->id=$newuser;
				$xml->user[count($xml->user)-1]->pwd=$newpwd;
				$xml->user[count($xml->user)-1]->desc=$newdesc;
				$xml->user[count($xml->user)-1]->role=$newrole;
				$xml->user[count($xml->user)-1]->supply=$newsupply;
				$xml->asXML("users.xml");
				echo "ok";
			}
		}
	}else if($action=="query"){
		$tmp=getUser($userid,$pwd);
		if($tmp&&$tmp->role=="1"){
			/*foreach($xml->user as $node){
				$node->pwd="";
			}*/
			header("Content-type: text/xml; charset=utf-8");
			echo $xml->asXML();
		}
	}else if($action=="del"){
		$tmp=getUser($userid,$pwd);
		if($tmp&&$tmp->role=="1"){
			$ind=$_REQUEST["index"];
			unset($xml->user[$ind]);
			echo $ind;
//			echo $xml->asXML();
//			$xml->asXML("users.xml");
	//		echo "ok";
		}
	}else if($action=="count"){
		$tmp=getUser($userid,$pwd);
		echo count($xml->user);
	}else if($action=="config"){
		$tmp=getUser($userid,$pwd);
		if($tmp){
			$x=$_REQUEST["x"];
			$y=$_REQUEST["y"];
			$w=$_REQUEST["w"];
			$h=$_REQUEST["h"];
			$tmp->barx=$x;
			$tmp->bary=$y;
			$tmp->barw=$w;
			$tmp->barh=$h;
			$xml->asXML("users.xml");
			echo "ok";
		}
	}
}else if($table=="supply"){
	if($action=="query"){
		$tmp=getUser($userid,$pwd);
		if($tmp){
			$arr=explode(",",$tmp->supply);
			if($arr[0]!=""){
				$result=new SimpleXMLElement("<vends/>");
				for($i=0;$i<count($arr);$i++){
					$result->addChild("vend","");
					$vend=$result->vend[count($result->vend)-1];
					$vend->code=$arr[$i];
					$vd=simplexml_load_file("data/".$arr[$i].".xml");
					$vend->desc=$vd["desc"];
					$vend->addChild("parts","");
					$parts=$vend->parts;
					foreach($vd->PartInfo as $node){
						$parts->addChild("part","");
						$part=$parts->part[count($parts->part)-1];
						$part->PartNo=$node->PartNo["value"];
						$part->PartDesc=$node->PartDesc["value"];
						$part->VendPartNo=$node->VendPartNo["value"];
						$part->VendPartDesc=$node->VendPartDesc["value"];
						$part->PackQTY=$node->PackQTY["value"];
						$part->OrderNO=$node->OrderNO["value"];
						$part->LocationNO=$node->LocationNO["value"];
						$part->PartNo=$node->PartNo["value"];
						$part->DrawSize=$node->DrawSize["value"];
						$part->Version=$node->Version["value"];
					}
				}
				header("Content-type: text/xml; charset=utf-8");
				echo $result->asXML();
			}
		}
	}else if($action=="download"){
		$tmp=getUser($userid,$pwd);
		if($tmp){
			$json=$_REQUEST["data"];
			$arr=json_decode($json);
			//change the filename to the timeintval.
			//$filename="uploads/".md5($json).".txt";
			$filename = microtime();
			$filename = explode(" ",$filename);
			$filename = "uploads/".$filename[1].intval($filename[0]*1000).".txt";
			
			$of = fopen($filename,'w');
			if ($of){
				for($i=0;$i<count($arr);$i++){
					fwrite($of,$arr[$i]."\r\n");
				}
			}
			fclose($of);
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".basename($filename));
			readfile($filename);
		}
	}
}
else if($table=="auddata"){
	$par=$_REQUEST["p"];
	$chd=$_REQUEST["c"];
	$pre=substr($par,0,6);
	$pre2=substr($chd,0,6);
	$filename="auddata/".$pre."-1.xml";
	if(file_exists($filename)){
		$xml2=simplexml_load_file($filename);
		foreach($xml2->data as $node){
			if($node->N05==$par&&$node->N13==$chd){
				header("Content-type: text/xml; charset=utf-8");
				$node->type="1";
				echo $node->asXML();
				return;
			}
		}
	}
	$flag=false;
	$dd=new SimpleXMLElement("<data/>");
	$dd->type="2";
	//check parent part.
	$filename2="auddata/".$pre."-1.xml";
	if(file_exists($filename2)){
		$xml3=simplexml_load_file($filename2);
		foreach($xml3->data as $node){
			if($node->N05==$par){
				$flag=true;
				$dd->N01=$node->N01;
				$dd->N02=$node->N02;
				$dd->N03=$node->N03;
				$dd->N04=$node->N04;
				$dd->N05=$node->N05;
				$dd->N06=$node->N06;
				$dd->N07=$node->N07;
				$dd->N08=$node->N08;
			}
		}
	}
	$filename2="auddata/".$pre."-2.xml";
	if(file_exists($filename2)){
		$xml3=simplexml_load_file($filename2);
		foreach($xml3->data as $node){
			if($node->N05==$par){
				$flag=true;
				$dd->N01=$node->N01;
				$dd->N02=$node->N02;
				$dd->N03=$node->N03;
				$dd->N04=$node->N04;
				$dd->N05=$node->N05;
				$dd->N06=$node->N06;
				$dd->N07=$node->N07;
				$dd->N08=$node->N08;
			}
		}
	}
	//check child part.
	$filename2="auddata/".$pre2."-1.xml";
	if(file_exists($filename2)){
		$xml3=simplexml_load_file($filename2);
		foreach($xml3->data as $node){
			if($node->N05==$chd){
				$flag=true;
				$dd->N09=$node->N01;
				$dd->N10=$node->N02;
				$dd->N11=$node->N03;
				$dd->N12=$node->N04;
				$dd->N13=$node->N05;
				$dd->N14=$node->N06;
				$dd->N15=$node->N07;
				$dd->N16=$node->N08;
			}
		}
	}
	$filename2="auddata/".$pre2."-2.xml";
	if(file_exists($filename2)){
		$xml3=simplexml_load_file($filename2);
		foreach($xml3->data as $node){
			if($node->N05==$chd){
				$flag=true;
				$dd->N09=$node->N01;
				$dd->N10=$node->N02;
				$dd->N11=$node->N03;
				$dd->N12=$node->N04;
				$dd->N13=$node->N05;
				$dd->N14=$node->N06;
				$dd->N15=$node->N07;
				$dd->N16=$node->N08;
			}
		}
	}
	if($flag){
		echo $dd->asXML();
		return;
	}
	
	echo "no";	
}

function getUser($u1,$p1){
	global $xml;
	if(!$xml)$xml=simplexml_load_file("users.xml");
	foreach($xml->user as $users){
		if($users->id==$u1&&$users->pwd==$p1){
			return $users;
		}
	}
	return null;
}




//print_r($xml);
?>