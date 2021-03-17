<?php


namespace AntCorp\MqttServer\Version;


use AntCorp\MqttServer\Protocol\Types;

class Common
{
    public function onReceive($server, $fd, $fromId, $data)
    {
        try {
            $pack = $this->unpack($data);

            if (is_array($pack) && isset($pack['type'])) {
                switch ($pack['type']) {
                    case Types::PINGREQ: // 心跳请求
                        [$class, $func] = $this->receiveCallbacks[Types::PINGREQ];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $pack)) {
                            // 返回心跳响应
                            $server->send($fd, $this->pack(['type' => Types::PINGRESP]));
                        }
                        break;
                    case Types::DISCONNECT: // 客户端断开连接
                        [$class, $func] = $this->receiveCallbacks[Types::DISCONNECT];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $pack)) {
                            if ($server->exist($fd)) {
                                $server->close($fd);
                            }
                        }
                        break;
                    case Types::CONNECT: // 连接
                    case Types::PUBLISH: // 发布消息
                    case Types::SUBSCRIBE: // 订阅
                    case Types::UNSUBSCRIBE: // 取消订阅
                        [$class, $func] = $this->receiveCallbacks[$pack['type']];
                        $obj = new $class($this);
                        $obj->{$func}($server, $fd, $fromId, $pack);
                        break;
                }
            } else {
                $server->close($fd);
            }
        } catch (Throwable $e) {
            echo "\033[0;31mError: {$e->getMessage()}\033[0m\r\n";
        }
    }
}
