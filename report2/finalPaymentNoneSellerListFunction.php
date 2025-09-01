<?php

function getUnderstack($id)
{
    global $conn;

    $sql = "SELECT
				p.pName
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			LEFT JOIN
				tPeopleInfo AS p ON p.pId= s.sUndertaker1
			WHERE
				cs.cCertifiedId = '" . $id . "'";
    $rs = $conn->Execute($sql);

    return $rs->fields['pName'];
}

function getBuyer($id, $col)
{
    global $conn;

    $sql = "SELECT cName,cIdentifyId FROM tContractBuyer WHERE cCertifiedId = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    $bueyr = array();
    if (!empty($rs->fields[$col])) {
        $buyer[] = $rs->fields[$col];
    }

    $sql = "SELECT cName,cIdentifyId FROM tContractOthers WHERE cCertifiedId = '" . $id . "' AND cIdentity =1";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if (!empty($rs->fields[$col])) {
            $buyer[] = $rs->fields[$col];
        }

        $rs->MoveNext();
    }

    return @implode('_', $buyer);
}

function getOwner($id, $col)
{
    global $conn;

    $sql = "SELECT cName,cIdentifyId FROM tContractOwner WHERE cCertifiedId = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    $owner = array();
    if (!empty($rs->fields[$col])) {
        $owner[] = $rs->fields[$col];
    }

    $sql = "SELECT cName,cIdentifyId FROM tContractOthers WHERE cCertifiedId = '" . $id . "' AND cIdentity = 2";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if (!empty($rs->fields[$col])) {
            $owner[] = $rs->fields[$col];
        }

        $rs->MoveNext();
    }

    return empty($owner) ? '' : implode('_', $owner);
}
