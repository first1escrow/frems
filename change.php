<?php

include_once 'openadodb.php' ;

$cat='buy';


switch ($cat) {
	case 'buy':
		change('tContractBuyer',6);
		break;
	case 'sell':
		change('tContractOwner',7);
		break;
	default:
		# code...
		break;
}


function change($tbl,$id)
{
	global $conn;

	$sql='SELECT cCertifiedId,cContactName FROM '.$tbl.' WHERE cContactName!="" ORDER BY cId ASC';

	$rs=$conn->Execute($sql);

	while(!$rs->EOF)
	{
		$list[]=$rs->fields;


		$rs->MoveNext();

	}

	foreach ($list as $k => $v) {

		$sql='INSERT INTO tContractOthers(cCertifiedId,cIdentity,cName) VALUES ("'.$v['cCertifiedId'].'","'.$id.'","'.$v['cContactName'].'")';
		
		
		if ($conn->Execute($sql)) {
			echo $sql."成功"."<br>";
		}else
		{
			echo $sql."<br>";
		}

	}
}





?>