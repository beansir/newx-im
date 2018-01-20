<h2 align="center">NewX IM</h2>

NewX IM是一个基于NewX框架的web即时通讯应用。

## 启动服务

编辑配置文件（web-socket项）

console/config/server.php

```php
<?php
return [
    // server config
    'server' => [
        'tcp' => [
            'host' => '0.0.0.0',
            'port' => 9501
        ],
        'web-socket' => [
            'host' => '0.0.0.0',
            'port' => 9502
        ],
        'http' => [
            'host' => '0.0.0.0',
            'port' => 9503
        ],
    ],
];
```

Linux系统环境下执行命令行
```
nx server web-socket
```