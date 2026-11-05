<?php

namespace App\Providers;

use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Support\ServiceProvider;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.sqlsrv', function () {
            $connector = new SqlServerConnector();
            $options = $connector->getDefaultOptions();

            // Some hosted PDO_SQLSRV builds reject this PDO attribute at connection
            // creation (IMSSP). Keep the workaround outside Composer's vendor files.
            unset($options[PDO::ATTR_STRINGIFY_FETCHES]);

            // Preserve integer/float fetch types using the SQL Server driver option.
            // DECIMAL/MONEY remain exact strings as defined by the driver.
            if (defined('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')) {
                $options[constant('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')] = true;
            }

            $connector->setDefaultOptions($options);

            return $connector;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
