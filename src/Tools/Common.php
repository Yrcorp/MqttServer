<?php

declare(strict_types=1);
/**
 * @description 惠州市蚁人科技
 * @link     https://www.huizhouyiren.com
 * @document https://wiki.huizhouyiren.com
 * @contact  hyc@huizhouyiren.com
 */
namespace AntCorp\MqttServer\Tools;

abstract class Common
{
    public static function printf(string $data)
    {
        echo "\033[36m";
        for ($i = 0; $i < strlen($data); ++$i) {
            $ascii = ord($data[$i]);
            if ($ascii > 31) {
                $chr = $data[$i];
            } else {
                $chr = ' ';
            }
            printf("%4d: %08b : 0x%02x : %d : %s\n", $i, $ascii, $ascii, $ascii, $chr);
        }
        echo "\033[0m";
    }
}
