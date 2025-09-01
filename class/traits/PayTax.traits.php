<?php
namespace First1\V1\Util;

trait PayTax
{
    /**
     * 所得稅額(回饋金)
     * 身分別(identity)：1=未知、2=身份證編號、3=統一編號、4=居留證號碼
     */
    public function feedbackIncomeTax($money, $identity)
    {
        $rate = 0;
        if (in_array($identity, [2, 3])) { //代書事務所或個人
            $rate = 0.1; //稅率：10%
        };

        if ($identity == 4) { //居留證號碼
            $rate = 0.2; //稅率：20%
        };

        if (is_null($rate)) {
            throw new \Exception('Can not verify tax rate');
        }

        return $this->calculate($money, $rate);
    }

    /**
     * 回饋金二代健保(回饋金)
     * 身分別(identity)：1=未知、2=身份證編號、3=統一編號、4=居留證號碼
     */
    public function feedbackNHITax($money, $identity)
    {
        if (($identity != 2) || ($money < 20000)) {
            return 0;
        }

        return $this->nhiCaculate($money);
    }

    /**
     * 扣繳稅款
     * identityNumber：證件編號
     */
    //所得稅額
    public function incomeTax($money, $identityNumber)
    {
        list($rate, $foreign) = $this->convertRateIdentity($identityNumber);

        if (is_null($rate)) {
            throw new \Exception('Can not verify tax rate');
        }

        return $this->calculate($money, $rate, $foreign);
    }

    //換算稅率與身分
    private function convertRateIdentity($identityNumber)
    {
        //外國籍自然人(一般民眾)
        if (preg_match("/[A-Za-z]{2}/", $identityNumber) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}$/", $identityNumber) || preg_match("/^9[0-9]{6}$/", $identityNumber)) {
            return [0.2, true]; //稅率：20%
        }
        ##

        //本國籍自然人(一般民眾)個人10碼、法人(公司)公司8碼
        if (preg_match("/^[a-zA-Z]{1}[0-9]{9}$/", $identityNumber) || preg_match("/^\d{8}$/", $identityNumber)) {
            return [0.1, false]; //稅率：10%
        }
        ##

        return [null, null];
    }

    //計算稅額
    private function calculate($money, $rate, $foreign = false)
    {
        if ($foreign == true) { //外國人不論金額大小都代扣稅
            return round($money * $rate);
        }

        return ($money <= 20000) ? 0 : round($money * $rate); //代扣稅額：10%、門檻：20,000
    }

    /**
     * 代扣二代健保
     * identityNumber：證件編號
     */
    //計算二代健保稅額 2016/01/15改1.91%(0.0191) //2021/01/01 調整為2.11%(0.0211)
    public function NHITax($money, $identityNumber)
    {
        list($rate, $foreign) = $this->convertRateIdentity($identityNumber);
        if ($foreign == true) { //外國人不代扣二代健保
            return 0;
        }

        if ($money < 20000) { //門檻限制：20,001
            return 0;
        }

        return preg_match("/^[a-zA-Z]{1}[0-9]{9}/", $identityNumber) ? $this->nhiCaculate($money) : 0; //二代健保代扣僅限本國自然人
    }

    //計算二代健保稅額
    private function nhiCaculate($money)
    {
        return ($money >= 20000) ? round($money * 0.0211) : 0; //二代健保：2.11%、門檻：20,000
    }
}
