<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Page;
use App\User;
use App\Visitor;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $visitsCount = 0;
        $onlineCount = 0;
        $pagesCount = 0;
        $usersCount = 0;
        $interval = intval($request->input('interval', 30));
        if ($interval > 120) {
            $interval = 120;
        }

        //contagem de visitantes
        $dateInterval = date('Y-m-d H:i:s', strtotime('-'.$interval.' days'));
        $visitsCount = Visitor::where('date_access', '>=', $dateInterval)
            ->count();

        //contagem de paginas
        $pagesCount = Page::count();

        //contagem de usuarios
        $usersCount = User::count();

        //contagem de usuarios online
        $datelimit = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $onlineList = Visitor::select('ip')
            ->where('date_access', '>=', $datelimit)
            ->groupBy('ip')
            ->get();
        $onlineCount = count($onlineList);

        //contagem para o PagePie
        $pagePie = [];
        $visitsAll = Visitor::selectRaw('page, count(page) as c')
            ->where('date_access', '>=', $dateInterval)
            ->groupBy('page')
            ->get();

        foreach ($visitsAll as $visit) {
            $pagePie[$visit['page']] = intval($visit['c']);
        }

        $pageLabels = json_encode(array_keys($pagePie));
        $pageValues = json_encode(array_values($pagePie));

        return view('admin.home', compact('visitsCount', 'onlineCount', 'pagesCount', 'usersCount', 
            'pageLabels', 'pageValues', 'interval'));
    }
}
