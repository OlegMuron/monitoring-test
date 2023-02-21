<?php
declare(strict_types=1);

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            Client::class,
            function () {
                $elasticConfig = config('elastic');

                $connection = $elasticConfig['connection'];

                $builder = ClientBuilder::create();
                $builder->setHosts($connection);

                return $builder->build();
            }
        );
    }
}
