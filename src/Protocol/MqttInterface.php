<?php

declare(strict_types=1);
/**
 * @description 惠州市蚁人科技
 * @link     https://www.huizhouyiren.com
 * @document https://wiki.huizhouyiren.com
 * @contact  hyc@huizhouyiren.com
 */
namespace AntCorp\MqttServer\Protocol;

interface MqttInterface
{
    // 1
    public function onMqConnect($server, int $fd, $fromId, $data);

    // 12
    public function onMqPingreq($server, int $fd, $fromId, $data): bool;

    // 14
    public function onMqDisconnect($server, int $fd, $fromId, $data): bool;

    // 3
    public function onMqPublish($server, int $fd, $fromId, $data);

    // 8
    public function onMqSubscribe($server, int $fd, $fromId, $data);

    // 10
    public function onMqUnsubscribe($server, int $fd, $fromId, $data);
}
