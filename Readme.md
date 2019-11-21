## Introduction
The Project is a PHP Library implementing redis client.

## Quick Start
```
        $config = [
            'host' => '127.0.0.1', //optional, default: 127.0.0.1
            'port' => 6379, //optional, default: 6379
            'timeout' => 1, //optional，default: 1ms
            'password' => null, //optional, default: null
            'db' => 10, //optional, default: null
        ];
        $client = new \SimpleRedis\Client($config);
        $client->set("test", 1);
        $res = $client->get("test");
        var_dump($res);
```