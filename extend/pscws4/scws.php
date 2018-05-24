<?php

/**
 * 中文分词处理方法
 * 词典下载 : http://www.xunsearch.com/scws/download.php
 * 词典解压 : tar -jvcf *.tar.bz2
 *+---------------------------------
 * @author Nzing
 * @access public
 * @version 1.0
 *+---------------------------------
 * @param stirng  $string 要处理的字符串
 * @param boolers $sort=false 根据value进行倒序
 * @param Numbers $top=0 返回指定数量，默认返回全部
 *+---------------------------------
 * @return void
 */
function scws($text, $top = 5, $return_array = false, $sep = ',') {
    include('./pscws4.php');
    $cws = new pscws4('utf-8');
    $cws -> set_charset('utf-8');
    $cws -> set_dict('./etc/dict.utf8.xdb');
    $cws -> set_rule('./etc/rules.utf8.ini');
    //$cws->set_multi(3);
    $cws -> set_ignore(true);
    //$cws->set_debug(true);
    //$cws->set_duality(true);
    $cws -> send_text($text);
    $ret = $cws -> get_tops($top, 'r,v,p');
    $result = null;
    foreach ($ret as $value) {
        if (false === $return_array) {
            $result .= $sep . $value['word'];
        } else {
            $result[] = $value['word'];
        }
    }
    return false === $return_array ? substr($result, 1) : $result;
}
// print_r(scws('青花夔龙纹香炉'));
print_r(scws('北京大学生喝进口红酒，在北京大学生活区喝进口红酒'));