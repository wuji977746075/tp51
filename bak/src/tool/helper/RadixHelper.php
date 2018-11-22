<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-02-10
 * Time: 10:57
 */

namespace app\src\tool\helper;


class RadixHelper
{
    /**
     * 十进制转36进制
     */
    public static function numTo36Hex($num){
        $num = intval($num);
        if ($num <= 0)
            return 0;
        //没有I，O
        $charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N',  'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $base = count($charArr);
        $char = '';
        do {
            $key = ($num) % $base;
            $char= $charArr[$key] . $char;
            $num = floor(($num - $key) / $base);
        } while ($num > 0);
        return $char;
    }

    /**
     * 进制转十进制
     * 不是36进制
     * 数字值不能太大
     * @param $hex36
     * @return int|string
     */
    public static function hex36ToNum($hex36){
        $result = 0;
        $len = strlen($hex36);
        //没有I，O
        $charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N',  'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $base = count($charArr);

        for ($i = $len; $i > 0; $i--)
        {
            $c = strtoupper(substr($hex36,$i-1,1));
            foreach ($charArr as $k=>$item){
                if($item == $c){
                    $result += $k * intval(pow($base, $len - $i));
                    break;
                }
            }
        }
        return $result;
    }
}