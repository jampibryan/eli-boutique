<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Foundation\Application;
use PDO;
use PHPUnit\Framework\TestCase;

class SqlServerConnectorTest extends TestCase
{
    private function factory(): ConnectionFactory
    {
        $app = new Application(dirname(__DIR__, 2));
        (new AppServiceProvider($app))->register();

        return new ConnectionFactory($app);
    }

    public function test_factory_omits_the_attribute_rejected_by_hosted_sqlsrv_drivers(): void
    {
        $connector = $this->factory()->createConnector(['driver' => 'sqlsrv']);
        $this->assertInstanceOf(SqlServerConnector::class, $connector);
        $options = $connector->getOptions([]);

        $this->assertArrayNotHasKey(PDO::ATTR_STRINGIFY_FETCHES, $options);
        $this->assertSame(PDO::ERRMODE_EXCEPTION, $options[PDO::ATTR_ERRMODE]);
        $this->assertSame(PDO::CASE_NATURAL, $options[PDO::ATTR_CASE]);
        $this->assertSame(PDO::NULL_NATURAL, $options[PDO::ATTR_ORACLE_NULLS]);
        if (defined('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')) {
            $this->assertTrue($options[constant('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')]);
        }
    }

    public function test_supported_custom_options_are_preserved(): void
    {
        $connector = $this->factory()->createConnector(['driver' => 'sqlsrv']);
        $options = $connector->getOptions(['options' => [PDO::ATTR_CASE => PDO::CASE_LOWER]]);

        $this->assertSame(PDO::CASE_LOWER, $options[PDO::ATTR_CASE]);
        $this->assertSame(PDO::ERRMODE_EXCEPTION, $options[PDO::ATTR_ERRMODE]);
        $this->assertArrayNotHasKey(PDO::ATTR_STRINGIFY_FETCHES, $options);
    }

    public function test_other_database_drivers_retain_their_original_options(): void
    {
        foreach (['mysql', 'sqlite'] as $driver) {
            $options = $this->factory()->createConnector(['driver' => $driver])->getOptions([]);
            $this->assertArrayHasKey(PDO::ATTR_STRINGIFY_FETCHES, $options);
            $this->assertFalse($options[PDO::ATTR_STRINGIFY_FETCHES]);
        }
    }
}
