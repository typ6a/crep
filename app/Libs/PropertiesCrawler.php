<?php

namespace App\Libs;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class PropertiesCrawler {

    protected $base_url = 'https://www.realtor.ca/Residential/Recreational/16448868/90-HIGHLAND-DR-ORO-MEDONTE-Ontario-L0L2L0';

    //protected $base_url = null;

    public function execute() {
        $url ='https://www.realtor.ca/Residential/Recreational/16448868/90-HIGHLAND-DR-ORO-MEDONTE-Ontario-L0L2L0';
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
        $property = new \App\Models\Property([
            'price' => $this->parsePropertyPrice(),
            'listingID' => $this->parsePropertyListingID(),
            'address' => $this->parsePropertyAddress()
        ]);

        pre(gettype($property->address),1);

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

            pre('1',1);

            //$url = $property->url;
            $this->crawler = new Crawler($html);
            $property->price = $this->parsePropertyPrice();

            if ($product->price == null) {
                $product->price=0;
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

    protected function parsePropertyAddress() {

        $items = $this->crawler->filter('#m_property_dtl_address_lft > h1');
        $address = null;
        foreach($items as $address){
            $address = $address->nodeValue;
        }
        //$address = trim($address);
        return $address;
    }
    protected function parsePropertyPrice() {
        $items = $this->crawler->filter('#m_property_dtl_info_hdr_price');
        $price = null;
        foreach($items as $price){
            $price = $price->nodeValue;
        }
        $price = preg_replace('/[^\d]/', '', $price);
        return $price;
    }

    protected function parsePropertyListingID() {
        $items = $this->crawler->filter('.m_property_dtl_info_hdr_lft_listingid');
        $listingID = null;
        foreach($items as $listingID){
            $listingID = $listingID->nodeValue;
        }
        $listingID = trim(str_replace ('Listing ID:', '', $listingID ));
        //$listingID = preg_replace('/[^\d]/', '', $listingID);
        return $listingID;
    }
    protected function parseProductImages($product) {
        $images = [];
        $items = $this->crawler->filter('.item_gal a > img');

        if ($items->count()) {
            $items->each(function (Crawler $image, $i) use (&$images, $product) {

                $url = str_replace('resizer2/6', 'resizer2/2', $image->attr('src'));
                $parts = explode('?', $url);
                $url = (string) array_shift($parts);
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

        } return $images;
    }

    protected function parseProductProperties($product) {
        //$productProperties = [];
        $properties = [];
        $propertyRows = $this->crawler->filter('.yeni_ipep_props_groups table tbody tr.prop_line');
        //pre($propertyRow,1);

        if ($propertyRows->count()) {
            $propertyRows->each(function (Crawler $propertyRow) use (&$properties, $product) {
                $propertyName = trim($propertyRow->filter('td:first-child')->text());

                $property = \App\Models\ProductProperty::where('name', $propertyName)->first();

                if (!$property) {
                    $property = new \App\Models\ProductProperty([
                        'name' => $propertyName
                    ]);
                }
                $property->save();

                $property_id = $property->id;

                $property = \App\Models\ProductToProductProperty::where('product_property_id', $property_id)->where('product_id', $product->id)->first();
                if (!$property) {
                    $properties[] = new \App\Models\ProductToProductProperty([
                        'product_property_id' => $property_id,
                        'value' => trim($propertyRow->filter('td:last-child')->text()),
                    ]);
                }
            });
        } return $properties;
    }

}
