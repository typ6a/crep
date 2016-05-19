<?php

namespace App\Libs;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class RewPropertiesCrawler
{

    protected $base_url = 'http://www.rew.ca/properties/365145/201-80-regatta-landing-victoria?property_browse=victoria-bc';

    //protected $base_url = null;

    public function execute()
    {
        $url = 'http://www.rew.ca/properties/R2060885/110-15875-20-avenue-surrey?property_browse=surrey-area-bc';
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
            'pictures' => $this->parsePropertyPictures(),
            'description' => $this->parsePropertyDescription(),

            'summary' => $this->parsePropertySummary(),

            'features' => $this->parsePropertyFeatures(),
            'buildingDetails' => $this->parsePropertyBuildingDetails(),
            'landDetails' => $this->parsePropertyLandDetails(),
            'realtor' => $this->parsePropertyRealtor(),

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
        $propertyAddress = [];
        //$propertyExtra = null;
        $propertyBeds =null;
        $propertyBedsItems = $this->crawler->filter('.summarybar.summarybar--property .col-xs-3.summarybar-item span')->eq(1);
        foreach ($propertyBedsItems as $propertyBeds) {
            $propertyBeds = $propertyBeds->nodeValue;
        }
        $propertyBeds = trim($propertyBeds);
        pre($propertyBeds, 1);

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
       // pre($propertyAddress, 1);
        return $propertyAddress;
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
        if ($realtorCells->count()) {
            $realtorCells->each(function (Crawler $realtorCell) use (&$realtorInfo) {

                $realtorName = null;
                $realtorTitle = null;
                $realtorLinks = [];
                $realtorOfficeDesignation = null;
                $realtorOfficeLinks = [];

                $realtorPicture = $realtorCell->filter('.m_property_dtl_realtor_info_lft a img');
                if ($realtorPicture->count()) {
                    $realtorPicture->each(function (Crawler $image) {
                        $filename = null;
                        $url = $image->attr('src');
                        $imageUrl = 'http://' . trim($url, '/');
                        $filename = md5($url) . '.jpg';
                        $images[] = [
                            'url' => $imageUrl,
                            'filename' => $filename,
                        ];
                        //pre($images->url,1);
                        $filepath = 'd:\workspace\crep\public\data\images\\' . $filename;
                        $data = file_get_contents($url);
                        file_put_contents($filepath, $data);
                    });
                }

                $realtorOfficePicture = $realtorCell->filter('.m_property_dtl_office_logo.noPrint a img');
                if ($realtorOfficePicture->count()) {
                    $realtorOfficePicture->each(function (Crawler $image) {
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

                $realtorOfficeTitleEl = $realtorCell->filter('#lblOfficeName');
                if ($realtorOfficeTitleEl->count()) {
                    $realtorOfficeTitle = trim($realtorOfficeTitleEl->text());
                }

                $realtorOfficeDesignationEl = $realtorCell->filter('.m_property_dtl_office_designation span');
                if ($realtorOfficeDesignationEl->count()) {
                    $realtorOfficeDesignation = trim($realtorOfficeDesignationEl->text());
                }

                $realtorOfficeAddressEl = $realtorCell->filter('.m_property_dtl_office_address span');
                if ($realtorOfficeAddressEl->count()) {
                    $realtorOfficeAddress = str_replace('<br>', ',', trim($realtorOfficeAddressEl->text()));
                }

                $realtorOfficeMediaLinks = $realtorCell->filter('.m_property_dtl_office_social a');
                if ($realtorOfficeMediaLinks->count()) {
                    $realtorOfficeMediaLinks->each(function (Crawler $a) use (&$realtorOfficeLinks) {
                        $realtorOfficeLinks[] = $a->attr('href');
                    });

                }
                $realtorOfficeCellPhones = $realtorCell->filter('.m_property_dtl_office_phone .m_realtor_dtl_phone_item span');
                if ($realtorOfficeCellPhones->count()) {
                    $realtorOfficeCellPhones->each(function (Crawler $span) use (&$realtorOfficePhones) {
                        $realtorOfficePhones[] = trim($span->text());
                    });
                }
                $realtorCellPhones = $realtorCell->filter('.m_property_dtl_realtor_phone .m_realtor_dtl_phone_item span');
                if ($realtorCellPhones->count()) {
                    $realtorCellPhones->each(function (Crawler $span) use (&$realtorPhones) {
                        $realtorPhones[] = trim($span->text());
                    });
                }

                $realtorTitleEl = $realtorCell->filter('#lblTitle');
                if ($realtorTitleEl->count()) {
                    $realtorTitle = trim($realtorTitleEl->text());
                }

                $realtorNameEl = $realtorCell->filter('#lnkRealtorDetails2 span');
                if ($realtorNameEl->count()) {
                    $realtorName = trim($realtorNameEl->text());
                }

                $realtorMediaLinks = $realtorCell->filter('.m_property_dtl_realtor_social a');
                if ($realtorMediaLinks->count()) {
                    $realtorMediaLinks->each(function (Crawler $a) use (&$realtorLinks) {
                        $realtorLinks[] = $a->attr('href');
                    });
                }


                $realtorInfo[] = [
                    'realtorName' => $realtorName,
                    'realtorTitle' => $realtorTitle,
                    'realtorLinks' => $realtorLinks,
                    'realtorOfficeTitle' => $realtorOfficeTitle,
                    'realtorOfficeDesignation' => $realtorOfficeDesignation,
                    'realtorOfficeAddress' => $realtorOfficeAddress,
                    'realtorOfficeLinks' => $realtorOfficeLinks,
                    'realtorPhones' => $realtorPhones,
                    'realtorOfficePhones' => $realtorOfficePhones


                ];

            });

        }
        //pre($realtorInfo,1);
        return $realtorInfo;
    }


    protected function parsePropertyBuildingDetails()
    {
        $buildingDetails = [];
        $featureColls = $this->crawler->filter('#rptBuildingDetails td');
        //pre(count($featureColls),1);
        if ($featureColls->count()) {
            $featureColls->each(function (Crawler $featureColl) use (&$buildingDetails) {
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
                    $buildingDetails[] = [
                        'name' => $featureName,
                        'value' => $featureValue
                    ];
                }
            });

        }
        //pre($features,1);
        return $buildingDetails;
    }


    protected function parsePropertyLandDetails()
    {
        $landDetails = [];
        $featureColls = $this->crawler->filter('#rptLandDetails td');
        //pre(count($featureColls),1);
        if ($featureColls->count()) {
            $featureColls->each(function (Crawler $featureColl) use (&$landDetails) {
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
                    $landDetails[] = [
                        'name' => $featureName,
                        'value' => $featureValue
                    ];
                }
            });

        }
        //pre($features,1);
        return $landDetails;
    }

}
