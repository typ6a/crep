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
        $urls = [
            'https://www.realtor.ca/Residential/Single-Family/16801705/60-HEMLOCK-Street-CARDIFF-Ontario-K0L1M0',
            'https://www.realtor.ca/Residential/Single-Family/16787381/RM-HURON-Elbow-Saskatchewan-S0H3P0',
            'https://www.realtor.ca/Commercial/Agriculture/16697307/N-12-12-19-15-W3rd-Saskatchewan-Landing-Saskatchewan-S0N2P0',
        ];
        $c = new \App\Libs\RealtorPropertiesCrawler();
        foreach($urls as $url){
            $c->execute($url);
        }
    }
    public function crawlRewProperties()
    {
        $urls = [
            'https://www.realtor.ca/Residential/Single-Family/16787381/RM-HURON-Elbow-Saskatchewan-S0H3P0',
            'https://www.realtor.ca/Commercial/Agriculture/16697307/N-12-12-19-15-W3rd-Saskatchewan-Landing-Saskatchewan-S0N2P0',
        ];
        $c = new \App\Libs\RewPropertiesCrawler();
        foreach($urls as $url){
            $c->execute($url);
        }
    }

}
