<?php
require_once __DIR__ . '/base.class.php';

class tax extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    //國籍判斷  1外國人 2本國人
    public function citizenship($_id)
    {
        $_len = strlen($_id); // 個人10碼 公司8碼

        if (preg_match("/[A-Za-z]{2}/", $_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $_id)) { //外國籍自然人(一般民眾)
            $_o   = 1; // 外國籍自然人(一般民眾)

        } else if ($_len == '10') { // 個人10碼
            if (preg_match("/[A-Za-z]{2}/", $_id)) { // 判別是否為外國人(兩碼英文字母者)
                $_o   = 1; // 外國籍自然人(一般民眾)
            } else {
                $_o   = 2; // 本國籍自然人(一般民眾)
            }
        } else if ($_len == '8') { // 公司8碼
            $_o   = 2; // 本國籍法人(公司)
        } else if ($_len == '7') {
            if (preg_match("/^9[0-9]{6}$/", $_id)) { // 判別是否為外國人
                $_o   = 1; // 外國籍自然人(一般民眾)
            }
        }
        return $_o;
    }

    //扣繳稅額
    public function interestTax($_o, $_int = 0)
    {
        if ($_o == "1") {
            $cTax = round($_int * 0.2);
        } else if ($_o == "2") {
            $cTax = 0;
            if ($_int > 20000) {
                $cTax = round($_int * 0.1);
            }
        }
        return $cTax;
    }

    //二代健保 (只有 本國籍 自然人 才要扣)
    public function NHITax($_int = 0)
    {
        $NHI = 0;
        if ($_int >= 20000) { // 105/01/01起額度改為20000
            $NHI = round($_int * 0.0211); // 則代扣 2% 保費 2016/01/15改1.91%(0.0191)
        }
        return $NHI;
    }
}