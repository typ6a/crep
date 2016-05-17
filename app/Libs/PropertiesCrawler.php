<?php

namespace App\Libs;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class PropertiesCrawler
{

    protected $base_url = 'https://www.realtor.ca/Residential/Recreational/16448868/90-HIGHLAND-DR-ORO-MEDONTE-Ontario-L0L2L0';

    //protected $base_url = null;

    public function execute()
    {
        $url = 'https://www.realtor.ca/Residential/Recreational/16448868/90-HIGHLAND-DR-ORO-MEDONTE-Ontario-L0L2L0';
        $html = file_get_contents($url);
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $this->crawler = new Crawler($html);
        //pre($html,1);
        /*
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $items = $xpath->query('*[@id="m_property_dtl_info_hdr_price"]');
        pre($items,1);
        pre($items->length,1);
        */
        //$property = null;
        $property = new \App\Models\Property([
            'price' => $this->parsePropertyPrice(),
            'listingID' => $this->parsePropertyListingID(),
            'address' => $this->parsePropertyAddress(),
            'description' => $this->parsePropertyDescription(),
            'features' => $this->parsePropertyFeatures(),
            'pictures' => $this->parsePropertyPictures(),
            'buildingDetails' => $this->parsePropertyBuildingDetails(),
            'realtor' => $this->parsePropertyRealtor()
        ]);

        //pre($property->features,1);

        $properties = \App\Models\Property::where('processed', 0)->get();
        foreach ($properties as $property) {
            /*
            $response = @file_get_contents($product->url);
            //pre($http_response_header,1);
            $response_code = (int) explode(' ', $http_response_header[0])[1];
            if (trim($response) || ($response_code > 400 && $response_code < 500)) {
                //$product->delete();
                continue;
            }
            */

            pre('1', 1);

            //$url = $property->url;
            $this->crawler = new Crawler($html);
            $property->price = $this->parsePropertyPrice();

            if ($product->price == null) {
                $product->price = 0;
            }

            $product->images()->saveMany($this->parseProductImages($product));

            $productProperties = $this->parseProductProperties($product);

            $product->properties()->saveMany($productProperties);


            $product->processed = 1;
            $product->save();
            //exit('sdfdsf');
            sleep(1);
        }
        pre('dine done', 1);
    }

    protected function parsePropertyAddress()
    {
        $items = $this->crawler->filter('#m_property_dtl_address');
        $address = null;
        foreach ($items as $address) {
            $address = $address->nodeValue;
        }
        //$address = trim($address);
        return $address;
    }

    protected function parsePropertyDescription()
    {
        $items = $this->crawler->filter('#m_property_dtl_gendescription');
        $description = null;
        foreach ($items as $description) {
            $description = $description->nodeValue;
        }
        $description = trim($description);
        return $description;
    }


    protected function parsePropertyPrice()
    {
        $items = $this->crawler->filter('#m_property_dtl_info_hdr_price');
        $price = null;
        foreach ($items as $price) {
            $price = $price->nodeValue;
        }
        $price = preg_replace('/[^\d]/', '', $price);
        return $price;
    }

    protected function parsePropertyListingID()
    {
        $items = $this->crawler->filter('.m_property_dtl_info_hdr_lft_listingid');
        $listingID = null;
        foreach ($items as $listingID) {
            $listingID = $listingID->nodeValue;
        }
        $listingID = trim(str_replace('Listing ID:', '', $listingID));
        //$listingID = preg_replace('/[^\d]/', '', $listingID);
        return $listingID;
    }

    protected function parseProductImages($product)
    {
        $images = [];
        $items = $this->crawler->filter('.item_gal a > img');

        if ($items->count()) {
            $items->each(function (Crawler $image, $i) use (&$images, $product) {

                $url = str_replace('resizer2/6', 'resizer2/2', $image->attr('src'));
                $parts = explode('?', $url);
                $url = (string)array_shift($parts);
                $imageUrl = 'http://' . trim($url, '/');
                $filename = $product->category_id . '.' . $i . '.jpg';
                $images[] = new \App\Models\ProductImage([
                    'url' => $imageUrl,
                    'filename' => $filename
                ]);
                // save image to local HDD
                $filepath = 'd:\workspace\leds\public\data\images\\' . $filename;

                file_put_contents($filepath, file_get_contents($imageUrl));
                //pre($images);


            });

        }
        return $images;
    }

    protected function parsePropertyFeatures()
    {
        $features = [];
        $featureColls = $this->crawler->filter('#rptFeatures td');
        //pre(count($featureColls),1);
        if ($featureColls->count()) {
            $featureColls->each(function (Crawler $featureColl) use (&$features) {
                $featureName = null;
                $featureValue = null;
                $featureNameEl = $featureColl->filter('span:first-child');
                if ($featureNameEl->count()) {
                    $featureName = trim($featureNameEl->text());
                }
                $featureValueEl = $featureColl->filter('span:last-child');
                if ($featureValueEl->count()) {
                    $featureValue = trim($featureValueEl->text());
                    //pre($featureValue);
                }
                if ($featureName && $featureValue) {
                    $features[] = [
                        'name' => $featureName,
                        'value' => $featureValue
                    ];
                }
            });

        }
        //pre($features,1);
        return $features;
    }

    protected function parsePropertyPictures()
    {
        $images = [];
        $items = $this->crawler->filter('#makeMeScrollable img');
        //pre($items->count(),1);
        if ($items->count()) {
            $items->each(function (Crawler $image) {
                $filename = null;
                $url = $image->attr('src');
                $imageUrl = 'http://' . trim($url, '/');
                $filename = md5($url) . '.jpg';
                $images[] = [
                    'url' => $imageUrl,
                    'filename' => $filename
                ];
                //pre($images->url,1);
                $filepath = 'd:\workspace\crep\public\data\images\\' . $filename;
                $data = file_get_contents($url);
                file_put_contents($filepath, $data);
            });
        }
        //exit('adsad');
        return $images;
    }

    protected function parsePropertyRealtor()
    {
        $realtorInfo = [];
        $realtorCells = $this->crawler->filter('#divRealtor .m_property_dtl_realtor_cell');
        //pre($realtorCell->count(),1);
        if ($realtorCells->count()) {
            $realtorCells->each(function (Crawler $realtorCell) use (&$realtorInfo) {
                $realtorName = null;
                $realtorTitle = null;
                $realtorLinks = [];

                $realtorTitleEl = $realtorCell->filter('#lblTitle');
                if ($realtorTitleEl->count()) {
                    $realtorTitle = trim($realtorTitleEl->text());
                }

                $realtorNameEl = $realtorCell->filter('#lnkRealtorDetails2 span');
                if ($realtorNameEl->count()) {
                    $realtorName = trim($realtorNameEl->text());
                }

                $realtorPhoneEl = $realtorCell->filter('#lblPhone_0');
                if ($realtorPhoneEl->count()) {
                    $realtorPhone = trim($realtorPhoneEl->text());
                }

                $realtorFaxEl = $realtorCell->filter('#lblPhone_1');
                if ($realtorFaxEl->count()) {
                    $realtorFax = trim($realtorFaxEl->text());
                }


                $realtorMediaLinks = $realtorCell->filter('.m_property_dtl_realtor_social');
                if ($realtorMediaLinks->count()) {
                    $realtorMediaLinks->each(function (Crawler $realtorMediaLink) use (&$realtorInfo){
                        $realtorLinkEl = $realtorMediaLink->filter('.m_realtor_dtl_contacts_rgt_media noPrint');
                        if ($realtorLinkEl->count()) {
                            $realtorLinks = $realtorLinkEl;

                    }
                    });



                }

                    $realtorInfo[] = [
                        'realtorName'   => $realtorName,
                        'realtorTitle'  => $realtorTitle,
                        'realtorPhone'  => $realtorPhone,
                        'realtorFax'    => $realtorFax,
                        'realtorLinks'  => $realtorLinks

                    ];

            });

        }
        pre($realtorInfo,1);
    }








protected
function parsePropertyBuildingDetails()
{
    $buildingDetails = null;
    return $buildingDetails;
}

}
