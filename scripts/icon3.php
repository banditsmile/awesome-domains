<?php

function downloadFavicon($domain, $outputDir, $logFile) {
    $protocols = ['https', 'http'];
    $providers = [
        'https://t1.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://'.$domain.'&size=256',
        'https://parental-peach-bat.faviconkit.com/'.$domain.'/256',
        'https://besticon-demo.herokuapp.com/icon?size=80..120..200&url='.$domain,
        'https://www.google.com/s2/favicons?domain_url=http://'.$domain,
        'https://icons.duckduckgo.com/ip2/'.$domain.'.ico',
//        'https://favicongrabber.com/api/grab/',

        'https://' . $domain . '/favicon.ico',
        'http://' . $domain . '/favicon.ico',
    ];
    $faviconPath = $outputDir . '/' . $domain . '.ico';
    if (file_exists($faviconPath)){
        return true;
    }

    foreach ($providers as $faviconUrl) {
//        $faviconUrl = $protocol . '://' . $domain . '/favicon.ico';
        $ch = curl_init($faviconUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略SSL证书错误

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            file_put_contents($faviconPath, $response);
            return true;
        }
    }

    // 如果两个协议都失败，记录错误信息
    $errorMessage = "Failed to download favicon for domain: $domain, HTTP code: $httpCode\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
    return false;
}

function processDomainsFromCsv($csvFile, $outputDir, $logFile, $batchSize = 1000, $pauseAfter = 100) {
    $handle = fopen($csvFile, 'r');
    $count = 0;
    $batchCount = 0;

    if ($handle) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $domain = $data[1]; // 假设域名在CSV的第一列
            downloadFavicon($domain, $outputDir, $logFile);

            $count++;
            $batchCount++;

            // 每处理一定数量的域名后暂停
            if ($batchCount === $batchSize) {
                echo "Processed $count domains. Pausing for 5 minutes...\n";
                sleep(5); // 暂停5分钟
                $batchCount = 0;
            }

            // 暂停以避免触发网络防火墙限制
            if ($count % $pauseAfter === 0) {
                echo "Pausing for 1 second to avoid firewall restrictions...\n";
                sleep(5);
            }
        }
        fclose($handle);
    }
}
// 检查是否有参数传递
if ($argc <= 1) {
    echo "请输入要处理的文件名。\n";
    exit();
}
// 使用示例
$csvFile = $argv[1]; // 你的CSV文件路径
if(!is_file($csvFile)){
    exit("文件不存在");
}
if(!is_readable($csvFile)){
    exit("文件不可读");
}
$pinfo= pathinfo($csvFile);
$baseName = $pinfo['filename'];
$outputDir = 'output'; // 输出目录
$logFile = 'logs/'.$baseName.'.log'; // 日志文件路径
echo $outputDir,PHP_EOL;
echo $logFile,PHP_EOL;

// 确保输出目录存在
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// 确保日志目录存在
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0777, true);
}

processDomainsFromCsv($csvFile, $outputDir, $logFile);

?>