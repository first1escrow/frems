<?php
require_once __DIR__ . '/base.class.php';

class Advance extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetStatusContract($cCaseStatus = null)
    {
        $sql  = "SELECT * FROM `tStatusCase` WHERE sName NOT IN ('法務簽結') Order by sId; ";

        if($cCaseStatus == '法務簽結' or $_SESSION['member_pDep'] == 6) {
            $sql  = "SELECT * FROM `tStatusCase` Order by sId; ";
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetStatusExpenditure()
    {
        $sql  = "SELECT * FROM `tStatusExpenditure` Order by sId; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetStatusIncome()
    {
        $sql  = "SELECT * FROM `tStatusIncome` Order by sId; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetStatusProcession()
    {
        $sql  = " SELECT * FROM `tStatusProcession` Order by sId;  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetObjKind()
    {
        $sql  = "SELECT * FROM  `tObjKind` order by oId;  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetObjUse()
    {
        $sql  = "SELECT * FROM `tObjUse` Order by uId;  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetMaterialsList()
    {
        $sql  = "SELECT * FROM `tBuildingMaterials` Order by bTypeId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategroyRealestate()
    {
        $sql  = "SELECT * FROM  `tCategoryRealestate` Order by cId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryBrand()
    {
        $sql  = "SELECT * FROM  `tBrand` Order by bId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCountryCode()
    {
        $sql  = "SELECT * FROM  `data_country` Order by cCountry;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryBranch($branch)
    {
        $sql  = " SELECT * FROM `tBranch` where bId = '" . $branch . "'   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryBuild()
    {
        $sql  = " SELECT * FROM  `tCategoryBuild`;    ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetCategoryBank($banks = null)
    {
        $sql_where = "";
        if ($banks != null) {
            $sql_where = " AND cId in ('" . implode("','", $banks) . "') ";
        }

        $sql = "SELECT * FROM `tCategoryBank` Where 1 ";
        $sql .= $sql_where;
        $sql .= " Order by cId;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetContractBank($col = array())
    {
        $colum = '';
        if (empty($col)) {
            $colum = 'cId,cBankCode,cBankFullName,cBranchFullName';
        } else {
            $colum = @implode(',', $col);
        }

        //,CONCAT(cBankFullName,cBranchFullName) AS bankName
        $sql  = "SELECT " . $colum . " FROM `tContractBank` Where cShow = 1 ORDER BY cOrder ASC";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            $result[$key]['bankName'] = $value['cBankFullName'] . "(" . $value['cBranchFullName'] . ")";
        }
        return $result;
    }

    public function GetCategoryException()
    {
        $sql  = "SELECT * FROM `tStatusException` Order by sId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryProcession()
    {
        $sql  = "SELECT * FROM  `tStatusProcession` Order by sId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetPeopleList()
    {
        $sql  = "SELECT * FROM  `tPeopleInfo` WHERE pJob = '1' Order by pId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryIncome()
    {
        $sql  = "SELECT * FROM  `tCategoryIncome` ORDER BY sSort ASC    ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryAreaMenu()
    {
        $options    = array();
        $usesplit   = $this->GetCategoryUseSplit();
        $options[0] = "------";
        foreach ($usesplit as $k => $v) {
            $ca                 = $this->GetCategoryArea($k);
            $options[$k + 5000] = $v;
            foreach ($ca as $k2 => $v2) {
                $options[$v2['cId']] = "&nbsp;&nbsp;&nbsp;&nbsp;" . $v2['cName'];
            }
        }
        return $options;
    }

    public function GetCategoryAreaMenuList()
    {
        $sql  = "SELECT cId, cName FROM `tCategoryArea`;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = [];
        foreach ($rs as $k => $v) {
            $options[$v['cId']] = $v['cName'];
        }

        return $options;
    }

    public function GetCategoryArea($category = 0)
    {
        $sql  = " SELECT * FROM  `tCategoryArea` Where cCategory = '" . $category . "' ORDER BY cId  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryLand()
    {
        $sql  = " SELECT * FROM  `tCategoryLand` ORDER BY nid ASC;    ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetBrandList()
    {
        $sql  = " SELECT * FROM `tBrand` Order by bId desc  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetCategoryGuild()
    {
        $sql  = " SELECT * FROM `tCategoryGuild` Order by cId;  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetSmsTitle($tkind = null, $id = null)
    {
        $arr_kind  = array();
        $sql       = "";
        $sql_where = "";
        $sql_main  = "";
        $sql_order = "";
        $loop_key  = 3;

        $sql_main = " SELECT * FROM  `tTitle_SMS` WHERE 1 ";

        for ($i = 0; $i <= $loop_key; $i++) {
            if (($tkind & (1 << $i)) > 0) {
                $arr_kind[] = $i;
            }
        }

        $sql_where .= " AND tKind in (" . implode(",", $arr_kind) . ")";

        if ($id != null) {
            $sql_where .= " AND id = '" . $id . "' ";
        }

        $sql_order = " ORDER BY tKind, id ";

        $sql = $sql_main . $sql_where . $sql_order;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetSmsMain($kind)
    {
        $sql       = "";
        $sql_where = "";
        $sql_order = "";
        $arr_kind  = array();

        for ($i = 0; $i <= 3; $i++) {
            if (($kind & (1 << $i)) > 0) {
                $arr_kind[] = "'" . $i . "'";
            }
        }

        $sql_where .= " AND tKind in (" . implode(",", $arr_kind) . ")";

        $sql = "SELECT
                    a.id, a.tKind, a.tTitle, b.tPID, b.tName, b.tMobile, b.tCreateTime
                FROM
                    (SELECT * FROM  `tTitle_SMS` Where 1 " . $sql_where . " ) a
                    LEFT JOIN
                    tMain_SMS b ON a.id = b.tNID ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetBankMenuList($list = array())
    {
        /*
        $sql = " SELECT
        bId, replace(concat(bBank3, bBank4), '　', '') bBank, replace(bBank4_name, '　', '') bBank4_name
        FROM
        `tBank`
        WHERE 1
        AND bBank4 = ''
        AND bCodeTitle Not in ('共用中心', '漁會') ";
         */
        $sql = " SELECT
                    bId, replace(concat(bBank3, bBank4), '　', '') bBank, replace(bBank4_name, '　', '') bBank4_name
                 FROM
                    `tBank`
                 WHERE 1
                    AND bBank4 = ''
                    AND bOK = 0
				 ORDER BY
					bBank3,bBank4
				 ASC;";
        if (!empty($list)) {
            $sql .= " AND bBank3 IN (" . implode(',', $list) . ") ";
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return parent::ConvertBankOption($result, 'bBank', 'bBank4_name', true);
    }

    public function GetBankBranchList($code = null)
    {
        $sql = " SELECT replace(bBank3, '　', '') bBank3, replace(bBank4, '　', '') bBank4, bBank4_name
                 FROM
                    `tBank`
                 WHERE 1
                    AND bBank3 like '%" . $code . "%'
                    AND bBank4 != ''
                    AND bOK = 0
				 ORDER BY
					bBank3,bBank4
				 ASC; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return parent::ConvertBankOption($result, 'bBank4', 'bBank4_name');
    }

    public function DoSql($sql)
    {
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function GetSql($sql)
    {
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

}
