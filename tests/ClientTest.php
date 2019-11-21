<?php

use SimpleRedis\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    /**
     * @var Client
     */
    private $client;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $config = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1, //1ms
            'db' => 10,
        ];
        $this->client = new Client($config);
    }

    public function testInstance()
    {
        $config = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1, //1ms
            'db' => 10,
        ];
        $client = new Client($config);
        $this->assertInstanceOf(Client::class, $client);

        $config = [
            'port' => 27198,
        ];
        try {
            $client1 = new Client($config);
            $client1->select(1);
        } catch (\Exception $e) {
            $client1 = $e;
        }
        $this->assertInstanceOf(\Exception::class, $client1);
    }

    public function testAuth()
    {
        try {
            $response = $this->client->auth("1");
        } catch (\Exception $e) {
            $response = $e;
        }
        $this->assertSame(true, $response instanceof \Exception);
    }

    public function testSet()
    {
        $key = "test1\r\n";
        $response = $this->client->set($key, "\r\nd\r\n");
        $this->assertSame("OK", $response);
    }

    public function testGet()
    {
        $key = "\r\ntest2\r\n";
        $values = ["df\r\n=22", "\r\nd\r\n"];

        foreach ($values as $value) {
            $this->client->set($key, $value);
            $response = $this->client->get($key);
            $this->assertSame($value, $response);
        }
    }

    public function testDel()
    {
        $key = "test:" . microtime();
        $response = $this->client->del($key);
        $this->assertSame(0, $response);

        $this->client->set($key, "1");

        $response = $this->client->del($key);
        $this->assertSame(1, $response);
    }

    public function testHSet()
    {
        $key = "test3";
        $response = $this->client->del($key);
        $this->assertSame(true, $response == 0 || $response == 1);

        $response = $this->client->hgetall($key);
        $this->assertSame([], $response);

        $response = $this->client->hset($key, "a", "ab\r\nd3");
        $this->assertSame(1, $response);

        $response = $this->client->hgetall($key);
        $this->assertSame(["a" => "ab\r\nd3"], $response);

        $response = $this->client->hset($key, "b", "f");
        $this->assertSame(1, $response);

        $response = $this->client->hgetall($key);
        $this->assertSame(["a" => "ab\r\nd3", "b" => "f"], $response);
    }

    public function testHScan()
    {
        $key = "\r\ntes\r\nt4\r\n";
        $this->client->del($key);
        $this->client->hset($key, "a", "ab\r\nd3");
        $this->client->hset($key, "b", "f222");

        $response = $this->client->hscan($key, 0);
        $this->assertSame(["0", ["a" => "ab\r\nd3", "b" => "f222"]], $response);
    }

    public function testHMGet()
    {
        $key = "test5";
        $this->client->del($key);
        $this->client->hset($key, "b", "f222");

        $response = $this->client->hmget($key, "c", "b");
        $this->assertSame([null, "f222"], $response);

        $response = $this->client->hmget($key, ["c", "b"]);
        $this->assertSame([null, "f222"], $response);
    }

    public function testSAdd()
    {
        $key = "test6";
        $this->client->del($key);
        $response = $this->client->sadd($key, "b", "c\r\n", "d");
        $this->assertSame(3, $response);
    }

    public function testSScan()
    {
        $key = "test6";
        $this->client->del($key);
        $this->client->sadd($key, "b", "c\r\n", "d");

        $response = $this->client->sscan($key, 0, "COUNT", 4);
        sort($response[1]);
        $this->assertSame(["0", ["b", "c\r\n", "d"]], $response);

        $response = $this->client->sscan($key, 0, ["COUNT" => 10]);
        sort($response[1]);
        $this->assertSame(["0", ["b", "c\r\n", "d"]], $response);
    }

    public function testZAdd()
    {
        $key = "test7";
        $this->client->del($key);
        $response = $this->client->zadd($key, ["a" => 3, "b" => 4]);
        $this->assertSame(2, $response);
    }

    public function testZRange()
    {
        $key = "test7";
        $this->client->del($key);
        $this->client->zadd($key, ["a" => 3, "b" => 4]);

        $response = $this->client->zrange($key, 0, -1);
        $this->assertSame(["a", "b"], $response);

        $response = $this->client->zrange($key, 0, -1, "WITHSCORES");
        $this->assertSame(["a" => "3", "b" => "4"], $response);
    }

    public function testZScan()
    {
        $key = "test8";
        $this->client->del($key);
        $this->client->zadd($key, ["a\r\n" => 3, "b" => 4]);

        $response = $this->client->zscan($key, 0);
        $this->assertSame(["0", ["a\r\n" => "3", "b" => "4"]], $response);
    }

    public function testInfo()
    {
        $response = $this->client->info();
        $this->assertSame(true, is_string($response));
    }

    public function testClose()
    {
        $this->client->close();
        $response = $this->client->set("test1", 1);
        $this->assertSame("OK", $response);
    }

    public function testCommand()
    {
        try {
            $response = $this->client->command();
        } catch (\Exception $e) {
            $response = $e;
        }
        $this->assertInstanceOf(\Exception::class, $response);
    }

}