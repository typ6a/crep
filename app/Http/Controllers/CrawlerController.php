<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CrawlerController extends Controller
{

    public function __construct()
    {
        header('Content-Type: text/html; charset=utf-8');
        set_time_limit(36000);
    }

    public function crawlRealtorProperties()
    {
        $c = new \App\Libs\RealtorPropertiesCrawler();
        $c->execute();
    }
    public function crawlRewProperties()
    {
        $c = new \App\Libs\RewPropertiesCrawler();
        $c->execute();
    }

}
