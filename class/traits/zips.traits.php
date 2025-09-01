<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

trait Zips
{
    /**
     * 取得縣市列表
     */
    public static function getCity()
    {
        $conn = new first1DB;

        $sql = 'SELECT zCity as city FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;';
        $rs  = $conn->all($sql);

        return array_column($rs, 'city');
    }

    /**
     * 依據縣市取得所屬的鄉鎮市區
     * key 為郵遞區號
     */
    public static function getDistrict($city)
    {
        $conn = new first1DB;

        $sql = 'SELECT zZip as zip, zArea as district FROM tZipArea WHERE zCity = :city ORDER BY zZip ASC;';
        $rs  = $conn->all($sql, ['city' => $city]);

        $district = [];
        foreach ($rs as $v) {
            $district[$v['zip']] = $v['district'];
        }

        return $district;
    }

    /**
     * 依據縣市區域查找郵遞區號
     */
    public static function getZipByName($city, $district)
    {
        $conn = new first1DB;

        $sql = 'SELECT zZip as zip FROM tZipArea WHERE zCity = :city AND zArea = :district;';
        $rs  = $conn->one($sql, ['city' => $city, 'district' => $district]);

        return empty($rs['zip']) ? null : $rs['zip'];
    }

    /**
     * 依據郵遞區號查找郵遞區號
     */
    public static function getCityDistrictByZip($zip)
    {
        $conn = new first1DB;

        $sql = 'SELECT zCity as city, zArea as district FROM tZipArea WHERE zZip = :zip;';
        $rs  = $conn->one($sql, ['zip' => $zip]);

        return empty($rs) ? null : ['city' => $rs['city'], 'district' => $rs['district']];
    }
}
