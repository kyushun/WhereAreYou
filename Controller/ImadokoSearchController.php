<?php

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ImadokoSearchController
{
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function index(Request $request, Response $response)
    {
        $query = $request->getQueryParam('q');

        if (!$query) {
            return $this->app->view->render($response, 'toppage');
        }

        
        $calendars = array();
        $numbers = array();
        $exceedQueries = false;
        $googleApi = new GoogleApi;
        $allUsers = DomainUsers::LoadDomainUsers();
        $queryUsers = [];

        // 検索クエリを区切り単語で分割
        $qs = explode(',', $query);

        // 複数検索の場合はあいまい検索が1件のみ 
        $__count = 0;
        if (count($qs) === 1) {
            $queryUsers = DomainUsers::searchUsers($qs[0], MAX_QUERIES);
        } else {
            foreach ($qs as $q) {
                if ($__count >= MAX_QUERIES) {
                    $exceedQueries = true;
                    break;
                }
                $queryUsers = array_merge($queryUsers, DomainUsers::searchUsers($q, 1));
                $__count++;
            }
        }

        foreach ($queryUsers as $qu) {
            if (isset($qu['ignoreEmailSearch']) && $qu['ignoreEmailSearch'] == true) {
                $__cal = new Calendar();
                $__cal->setName($qu['name']);
                $calendars[] = $__cal;
            } else {
                $calendars[] = $googleApi->getCalendar($qu['email']);
            }
            $numbers[] = isset($qu['phone']) ? $qu['phone'] : [];
        }

        return $this->app->view->render($response, 'search', compact('query', 'allUsers', 'exceedQueries', 'calendars', 'numbers'));
    }
}