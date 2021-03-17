<?php

declare(strict_types=1);
/**
 * @description 惠州市蚁人科技
 * @link     https://www.huizhouyiren.com
 * @document https://wiki.huizhouyiren.com
 * @contact  hyc@huizhouyiren.com
 */
namespace AntCorp\MqttServer\Events;

use AntCorp\MqttServer\Protocol\MqttInterface;
use AntCorp\MqttServer\Protocol\Types;

class MqttServer implements MqttInterface
{
    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function onMqConnect($server, int $fd, $fromId, $data)
    {
        // v3 回复格式
        $server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::CONNACK,
                    'code' => 0,
                    'session_present' => 0,
                ]
            )
        );

        // v5 回复格式
        /*$server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::CONNACK,
                    'code' => 0,
                    'session_present' => 0,
                    'properties' => [
                        'maximum_packet_size' => 1048576,
                        'retain_available' => true,
                        'shared_subscription_available' => true,
                        'subscription_identifier_available' => true,
                        'topic_alias_maximum' => 65535, //0
                        'wildcard_subscription_available' => true,
                    ],
                ]
            )
        );*/
    }

    public function onMqPingreq($server, int $fd, $fromId, $data): bool
    {
        // v3 回复格式
        $server->send($fd, $this->parent->pack(['type' => Types::PINGRESP]));

        // v5 回复格式
        /*$server->send($fd, $this->parent->pack(['type' => Types::PINGRESP]));*/
        return true;
    }

    public function onMqDisconnect($server, int $fd, $fromId, $data): bool
    {
        if ($server->exist($fd)) {
            $server->close($fd);
        }
        return true;
    }

    public function onMqPublish($server, int $fd, $fromId, $data)
    {
        var_dump($data);
        // v3
        foreach ($server->connections as $sub_fd) {
            if ($sub_fd != $fd) {
                $server->send(
                    $sub_fd,
                    $this->parent->pack(
                        [
                            'type' => $data['type'],
                            'topic' => $data['topic'],
                            'message' => $data['message'],
                            'dup' => $data['dup'],
                            'qos' => $data['qos'],
                            'retain' => $data['retain'],
                            'message_id' => $data['message_id'] ?? '',
                        ]
                    )
                );
            }
        }

        if ($data['qos'] === 1) {
            $server->send(
                $fd,
                $this->parent->pack(
                    [
                        'type' => Types::PUBACK,
                        'message_id' => $data['message_id'] ?? '',
                    ]
                )
            );
        }

        // v5
        /*foreach ($server->connections as $sub_fd) {
            if ($sub_fd != $fd) {
                $server->send(
                    $sub_fd,
                    $this->parent->pack(
                        [
                            'type' => $data['type'],
                            'topic' => $data['topic'],
                            'message' => $data['message'],
                            'dup' => $data['dup'],
                            'qos' => $data['qos'],
                            'retain' => $data['retain'],
                            'message_id' => $data['message_id'] ?? '',
                        ]
                    )
                );
            }
        }

        if ($data['qos'] === 1) {
            $server->send(
                $fd,
                $this->parent->pack(
                    [
                        'type' => Types::PUBACK,
                        'message_id' => $data['message_id'] ?? '',
                    ]
                )
            );
        }*/
    }

    public function onMqSubscribe($server, int $fd, $fromId, $data)
    {
        // v3
        $payload = [];
        foreach ($data['topics'] as $k => $qos) {
            if (is_numeric($qos) && $qos < 3) {
                $payload[] = $qos;
            } else {
                $payload[] = 0x80;
            }
        }
        $server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::SUBACK,
                    'message_id' => $data['message_id'] ?? '',
                    'codes' => $payload,
                ]
            )
        );

        // v5
        /*$payload = [];
        foreach ($data['topics'] as $k => $option) {
            $qos = $option['qos'];
            if (is_numeric($qos) && $qos < 3) {
                $payload[] = $qos;
            } else {
                $payload[] = \Simps\MQTT\Hex\ReasonCode::QOS_NOT_SUPPORTED;
            }
        }
        $server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::SUBACK,
                    'message_id' => $data['message_id'] ?? '',
                    'codes' => $payload,
                ]
            )
        );*/
    }

    public function onMqUnsubscribe($server, int $fd, $fromId, $data)
    {
        // v3
        $server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::UNSUBACK,
                    'message_id' => $data['message_id'] ?? '',
                ]
            )
        );

        // v5
        /*$server->send(
            $fd,
            $this->parent->pack(
                [
                    'type' => Types::UNSUBACK,
                    'message_id' => $data['message_id'] ?? '',
                ]
            )
        );*/
    }
}
