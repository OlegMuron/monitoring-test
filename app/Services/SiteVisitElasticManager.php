<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\SiteVisit;
use Elasticsearch\Client as ElasticClient;
use Illuminate\Support\Facades\Log;

class SiteVisitElasticManager
{
    /**
     * @var string
     */
    private string $index;

    /**
     * @param ElasticClient $elasticClient
     */
    public function __construct(private ElasticClient $elasticClient)
    {
        $this->index = config('elastic.indices.site_visit.index');
    }

    /**
     * @param SiteVisit $siteVisit
     *
     * @return void
     */
    public function store(SiteVisit $siteVisit): void
    {
        $params = [
            'index' => $this->index,
            'id' => $siteVisit->id,
            'body' => [
                'url' => $siteVisit->url,
                'ip_address' => $siteVisit->ip_address,
                'user_agent' => $siteVisit->user_agent,
            ],
        ];

        $this->elasticClient->create($params);
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function get(string $id): array
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
        ];


        $found = $this->elasticClient->get($params);

        return $found['_source'];
    }

    /**
     *
     * @return int
     */
    public function count(): int
    {
        $params = [
            'index' => $this->index,
        ];

        return $this->elasticClient->count($params)['count'];
    }
}
