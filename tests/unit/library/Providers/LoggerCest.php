<?php

namespace Canvas\Tests\unit\library\Providers;

use Monolog\Logger;
use Canvas\Providers\ConfigProvider;
use Canvas\Providers\LoggerProvider;
use Phalcon\Di\FactoryDefault;
use UnitTester;

class LoggerCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new LoggerProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('log'));
        /** @var Logger $logger */
        $logger = $diContainer->getShared('log');
        $I->assertTrue($logger instanceof Logger);
        $I->assertEquals('api-logger', $logger->getName());
    }
}
