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

    public function crawlProperties()
    {
        $c = new \App\Libs\PropertiesCrawler();
        $c->execute();
    }

}
