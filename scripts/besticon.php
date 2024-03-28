<?php
// Path: scripts/besticon.php
// 从给定的$url
function besticon($url,$format=['png','ico']){
    //设置一个发起http请求的agent
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    // 使用这个agent 通过http请求获取网站的html内容
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $html = curl_exec($ch);
    curl_close($ch);
    // 通过http请求返回的 状态码来判断请求是否成功
    if ($html === false) {
        return false;
    }
    // 通过正则表达式匹配html内容中所有meta标签的元素
    preg_match_all('/<link[^>]*?rel="icon"[^>]*?>/i', $html, $matches);
    // 如果没有匹配到meta标签则返回false
    if (empty($matches[0])) {
        return false;
    }
    // 通过正则表达式匹配meta标签中的href属性
    preg_match_all('/href="([^"]*)"/i', implode('', $matches[0]), $matches);
    // 如果没有匹配到href属性则返回false
    if (empty($matches[1])) {
        return false;
    }
    // 通过正则表达式匹配到的href属性值
    $icons = $matches[1];
    // 以json的格式返回这个结果给请求的用户
    $result = [];
    foreach ($icons as $icon) {
        $result[] = $icon;
    }
    return $result;
}


// Path: scripts/besticon.php
$url = 'https://icon.horse';
besticon($url);