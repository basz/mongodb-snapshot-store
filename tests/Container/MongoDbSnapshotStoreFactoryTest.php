<?php
/**
 * This file is part of the prooph/mongodb-snapshot-store.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\MongoDb\SnapshotStore\Container;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Prooph\MongoDb\SnapshotStore\Container\MongoDbSnapshotStoreFactory;
use Prooph\MongoDb\SnapshotStore\MongoDbSnapshotStore;
use ProophTest\MongoDb\SnapshotStore\TestUtil;

class MongoDbSnapshotStoreFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_adapter_via_connection_service(): void
    {
        $config['prooph']['mongodb_snapshot_store']['default'] = [
            'mongo_client_service' => 'my_connection',
        ];

        $client = TestUtil::getClient();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('my_connection')->willReturn($client)->shouldBeCalled();
        $container->get('config')->willReturn($config)->shouldBeCalled();

        $factory = new MongoDbSnapshotStoreFactory();
        $snapshotStore = $factory($container->reveal());

        $this->assertInstanceOf(MongoDbSnapshotStore::class, $snapshotStore);
    }

    /**
     * @test
     */
    public function it_creates_adapter_via_connection_options(): void
    {
        $config['prooph']['mongodb_snapshot_store']['custom'] = [
            'connection_options' => TestUtil::getConnectionParams(),
        ];

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn($config)->shouldBeCalled();

        $snapshotStoreName = 'custom';
        $snapshotStore = MongoDbSnapshotStoreFactory::$snapshotStoreName($container->reveal());

        $this->assertInstanceOf(MongoDbSnapshotStore::class, $snapshotStore);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_container_given(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $eventStoreName = 'custom';
        MongoDbSnapshotStoreFactory::$eventStoreName('invalid container');
    }
}