<?php

namespace App\Libs;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class RewPropertiesCrawler
{

    protected $base_url = 'http://www.rew.ca/properties/365145/201-80-regatta-landing-victoria?property_browse=victoria-bc';

    //protected $base_url = null;

    public function execute()
    {
        $url = 'http://www.rew.ca/properties/408997/17-9933-chemainus-road-chemainus?property_browse=chemainus-bc';
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
        $property = [
            'price' => $this->parsePropertyPrice(),
            'listingID' => $this->parsePropertyListingID(),
            'address' => $this->parsePropertyAddress(),
            'pictures' => $this->parsePropertyImages(),
            'description' => $this->parsePropertyDescription(),
            'summary' => $this->parsePropertySummary(),
            'overview' => $this->parsePropertyOverview(),


        ];

        //pre($property, 1);

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
        $propertyAddress = [];
        //$propertyExtra = null;

        $propertyStreetAddresses = $this->crawler->filter('.propertyheader-address span[itemprop="streetAddress"]');
        foreach ($propertyStreetAddresses as $propertyStreetAddress) {
            $propertyStreetAddress = $propertyStreetAddress->nodeValue;
        }
        $propertyStreetAddress = trim($propertyStreetAddress);

        $propertyAddressesLocality = $this->crawler->filter('.propertyheader-secondary.propertyheader-piped_list li span[itemprop="addressLocality"]');
        foreach ($propertyAddressesLocality as $propertyAddressLocality) {
            $propertyAddressLocality = $propertyAddressLocality->nodeValue;
        }
        $propertyAddressLocality = trim($propertyAddressLocality);

        $propertyAddressesRegion = $this->crawler->filter('.propertyheader-secondary.propertyheader-piped_list li span[itemprop="addressRegion"]');
        foreach ($propertyAddressesRegion as $propertyAddressRegion) {
            $propertyAddressRegion = $propertyAddressRegion->nodeValue;
        }
        $propertyAddressRegion = trim($propertyAddressRegion);

        $propertyPostalCodes = $this->crawler->filter('.propertyheader-secondary.propertyheader-piped_list li span[itemprop="postalCode"]');
        foreach ($propertyPostalCodes as $propertyPostalCode) {
            $propertyPostalCode = $propertyPostalCode->nodeValue;
        }
        $propertyPostalCode = trim($propertyPostalCode);

        $propertyExtras = $this->crawler->filter('.propertyheader-secondary.propertyheader-piped_list li')->eq(1);
        foreach ($propertyExtras as $propertyExtra) {
            $propertyExtra = $propertyExtra->nodeValue;
        }
        $propertyExtra = trim($propertyExtra);

        $propertyAddress = [
            'propertyStreetAddress' => $propertyStreetAddress,
            'propertyAddressLocality' => $propertyAddressLocality,
            'propertyAddressRegion' => $propertyAddressRegion,
            'propertyPostalCode' => $propertyPostalCode,
            'propertyExtra' => $propertyExtra
        ];
        //pre($propertyAddress, 1);
        return $propertyAddress;
    }

    protected function parsePropertySummary()
    {
        $propertySummary = [];
        //$propertyExtra = null;
        $propertyBeds = null;
        $propertyBedsItems = $this->crawler->filter('.summarybar.summarybar--property .col-xs-3.summarybar-item span')->eq(0);
        if ($propertyBedsItems->count()) {
            foreach ($propertyBedsItems as $propertyBeds) {
                $propertyBeds = $propertyBeds->nodeValue;
            }
            $propertyBeds = trim($propertyBeds);
        }

        $propertyBaths = null;
        $propertyBathsItems = $this->crawler->filter('.summarybar.summarybar--property .col-xs-3.summarybar-item span')->eq(1);
        if ($propertyBathsItems->count()) {
            foreach ($propertyBathsItems as $propertyBaths) {
                $propertyBaths = $propertyBaths->nodeValue;
            }
            $propertyBaths = trim($propertyBaths);
        }

        $propertySize = null;
        $propertySizeItems = $this->crawler->filter('.summarybar.summarybar--property .col-xs-3.summarybar-item span')->eq(2);
        if ($propertySizeItems->count()) {
            foreach ($propertySizeItems as $propertySize) {
                $propertySize = $propertySize->nodeValue;
            }
            $propertySize = trim($propertySize);
        }

        $propertyType = null;
        $propertyTypeItems = $this->crawler->filter('.summarybar.summarybar--property .col-xs-3.summarybar-item span')->eq(3);
        if ($propertyTypeItems->count()) {
            foreach ($propertyTypeItems as $propertyType) {
                $propertyType = $propertyType->nodeValue;
            }
            $propertyType = trim($propertyType);
        }

        $propertySummary = [
            'beds' => $propertyBeds,
            'baths' => $propertyBaths,
            'size' => $propertySize,
            'type' => $propertyType,
        ];
        //pre($propertySummary,1);
        return $propertySummary;
    }


    protected function parsePropertyListingID()
    {
        $propertyListingIDs = $this->crawler->filter('.propertyheader-secondary.propertyheader-piped_list li')->last();
        foreach ($propertyListingIDs as $propertyListingID) {
            $propertyListingID = $propertyListingID->nodeValue;
        }
        $propertyListingID = str_replace('Listing ID:', '', preg_replace('|\s+|', ' ', trim($propertyListingID)));
        return $propertyListingID;
    }


    protected function parsePropertyDescription()
    {
        $description = [];
        $propertyDescriptionTitles = $this->crawler->filter('.propertydetails-description_title');
        foreach ($propertyDescriptionTitles as $propertyDescriptionTitle) {
            $propertyDescriptionTitle = $propertyDescriptionTitle->nodeValue;
        }
        $propertyDescriptionTitle = trim($propertyDescriptionTitle);

        $propertyDescriptions = $this->crawler->filter('.propertydetails-description div[itemprop="description"]');
        foreach ($propertyDescriptions as $propertyDescription) {
            $propertyDescription = $propertyDescription->nodeValue;
        }
        $propertyDescription = trim($propertyDescription);
        $description = [
            'propertyDescriptionTitle' => $propertyDescriptionTitle,
            'propertyDescription' => $propertyDescription
        ];

        return $description;

    }


    protected function parsePropertyPrice()
    {
        $items = $this->crawler->filter('.propertyheader-price');
        $price = null;
        foreach ($items as $price) {
            $price = $price->nodeValue;
        }
        $price = trim(preg_replace('/[^\d]/', '', $price));
        //pre($price, 1);
        return $price;
    }



    protected function parsePropertyOverview()
    {
        $features = [];
        $featureRows = $this->crawler->filter('.contenttable tbody tr');
        if ($featureRows->count()) {
            $featureRows->each(function (Crawler $featureRow) use (&$features) {
                $featureName = null;
                $featureValue = null;
                $featureNameEl = $featureRow->filter('th');
                if ($featureNameEl->count()) {
                    $featureName = trim($featureNameEl->text());
                }
                $featureValueEl = $featureRow->filter('td');
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
        return $features;
    }
    protected function parsePropertyImages()
    {
        $images = [];
        $items = $this->crawler->filter('.galleria-thumbnails-container .galleria-thumbnails-list .galleria-thumbnails .galleria-image img');
        pre($items->count(),1);
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
                pre($images->url,1);
                $filepath = 'd:\workspace\crep\public\data\images\\' . $filename;
                $data = file_get_contents($url);
                file_put_contents($filepath, $data);
            });
        }
        exit('adsad');
        return $images;
    }

    protected function parsePropertyPictures()
    {
        $images = [];
        $items = $this->crawler->filter('.galleria-thumbnails-container  img');// do not works!!!!!!!!!!!!suka
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




}
