# 基于 hyperf 开发的 mqtt服务端

## 安装
```sh
$ composer require ant-corp/mqtt-server
```
## 使用

### 在 config/server.php 添加
```php
use AntCorp\MqttServer\Events\MqttServer;
use AntCorp\MqttServer\Protocol\Types;
use AntCorp\MqttServer\Version\V3;

[
    'name' => 'mqtt',
    'type' => Server::SERVER_BASE,
    'host' => '0.0.0.0',
    'version' => V3::class,
    'port' => 9601,
    'sock_type' => SWOOLE_SOCK_TCP,
    'callbacks' => [
        Event::ON_RECEIVE => [AntCorp\MqttServer\Server::class, 'onReceive'],
    ],
    'receiveCallbacks' => [
        Types::CONNECT => [MqttServer::class, 'onMqConnect'],
        Types::PINGREQ => [MqttServer::class, 'onMqPingreq'],
        Types::DISCONNECT => [MqttServer::class, 'onMqDisconnect'],
        Types::PUBLISH => [MqttServer::class, 'onMqPublish'],
        Types::SUBSCRIBE => [MqttServer::class, 'onMqSubscribe'],
        Types::UNSUBSCRIBE => [MqttServer::class, 'onMqUnsubscribe'],
    ],
    'settings' => [
        'open_mqtt_protocol' => true, // 启用 EOF 自动分包
        'package_eof' => "\r\n", // 设置 EOF 字符串
        'package_max_length' => 2000000,
    ],
]
```
### 特别鸣谢

[simps](https://github.com/simple-swoole/simps)
