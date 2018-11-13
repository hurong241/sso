<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/17/2018
 * Time: 10:38 PM
 */

namespace ZXUN\Web;

/**
 * Class VerifyCode
 * @package ZXUN\Web
 * 验证码
 */
class VerifyCode
{
    protected $CODE = "0123456789abcdefghijklmnopqrstuvwrszABCDEFGHIJKLMNOPQRSTUVWRSZ";
    private $value;
    /**
     * VerifyCode constructor.
     * 构造方法
     */
    function __construct()
    {
        $code = [];
        for($i=0;$i<strlen($this->CODE);$i++){
            array_push($code,substr($this->CODE,$i,1));
        }
        shuffle($code);
        $this->value = implode('',array_slice($code,0,4));
        //echo $this->code;
    }

    public function Draw($width,$height)
    {
        $img = imagecreate($width,$height);
        imagecolorallocate($img,222,222,222);
        $testcolor1 = imagecolorallocate($img,255,0,0);
        $testcolor2 = imagecolorallocate($img,51,51,51);
        $testcolor3 = imagecolorallocate($img,0,0,255);
        $testcolor4 = imagecolorallocate($img,255,0,255);

        for ($i = 0; $i < 4; $i++)
        {
        imagestring($img,12,8 + $i * 15,5,$this->value[$i],rand(1,4));
        //imagestring($img,rand(5,6),8 + $i * 15,rand(2,8),$this->value[$i],rand(1,4));
        }
        imagegif($img);
    }
}