<?php

declare(strict_types=1);
/**
 * @description 惠州市蚁人科技
 * @link     https://www.huizhouyiren.com
 * @document https://wiki.huizhouyiren.com
 * @contact  hyc@huizhouyiren.com
 */
namespace AntCorp\MqttServer;

use Psr\Container\ContainerInterface;

class Server
{
    protected $config;

    protected $unPackServer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    // 通过在构造函数的参数上声明参数类型完成自动注入
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // 获取mqtt配置文件
        if (! $this->config) {
            $servers = config('server.servers');
            $config = array_values(array_filter($servers, function ($arr) {
                return $arr['name'] == 'mqtt';
            }));
            if (! $config) {
                throw new \RuntimeException('ConfigInterface is missing in server mqtt.');
            }
            $this->config = $config[0];
        }
        // 判断使用mqtt协议类型
        $class = $this->config['version'];
        if ($this->container->has($class)) {
            $this->unPackServer = $this->container->get($class);
            $this->unPackServer->setCallbacks($this->config['receiveCallbacks']);
        }
    }

    public function onReceive($server, $fd, $fromId, $data)
    {
        $this->unPackServer->onReceive($server, $fd, $fromId, $data);
    }
}
