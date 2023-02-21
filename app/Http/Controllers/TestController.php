<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SiteVisit;
use App\Services\SiteVisitElasticManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * @param SiteVisitElasticManager $siteVisitElasticManager
     */
    public function __construct(private SiteVisitElasticManager $siteVisitElasticManager)
    {
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function test(Request $request): View
    {
        $currentVisitor = [
            'url' => $request->getRequestUri(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->header('user-agent', 'no user-agent'),
        ];
        $visitor = SiteVisit::create($currentVisitor);

        $this->siteVisitElasticManager->store($visitor);

        return view(
            'test',
            [
                'mongo' => [
                    'count' => SiteVisit::all()->count(),
                    'current_visitor' => $currentVisitor,
                ],
                'elastic' => [
                    'count' => $this->siteVisitElasticManager->count(),
                    'current_visitor' => $this->siteVisitElasticManager->get($visitor->id),
                ],
            ]
        );
    }
}
