<?php

namespace App\Libs;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class RealtorPropertiesCrawler
{

    //protected $base_url = null;

    public function execute($url)
    {
        //$properties = \App\Models\Property::where('processed', 0)->get();
        $html = file_get_contents($url);
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $this->crawler = new Crawler($html);
        $propertyData =
            [
                'listing_id' => $this->parsePropertyListingID(),
                'price' => $this->parsePropertyPrice(),
                'url' => $url,
                'address' => $this->parsePropertyAddress(),
                //'sales_type',
                'description' => $this->parsePropertyDescription(),
                'features' => $this->parsePropertyFeatures(),////noname table (Property Type, Building Type, Title, Land Size, Age Of Building, Parking Type,
                'images' => $this->parsePropertyImages(),
                'realtors' => $this->parsePropertyRealtors(),
                'propertyDetails' => $this->parsePropertyDetails(),
                'buildingDetails' => $this->parsePropertyBuildingDetails(),//building
                'landDetails' => $this->parsePropertyLandDetails(),//land
            ];

        $property = new \App\Models\Property
        ([
            'listing_id' => $propertyData['listing_id'],
            'price' => $propertyData['price'],
            'url' => $propertyData['url'],
            'address' => $propertyData['address'],
            'sale_type' => 'NA',
            'open_house' => 'NA'
        ]);

        //$property_id = $property->id;
        foreach ($propertyData['features'] as $f) {
            $propertyName = \App\Models\PropertyFeature::where('name', $f)->first();
            if (!$propertyName){
                $propertyName = new \App\Models\PropertyFeature(
                    ['name' => $f['name']]
                );
                $propertyName->save();
                $feature_id = $propertyName->id;
                $feature = \App\Models\PropertyToPropertyFeature::where('property_feature_id', $feature_id)->where('property_id', $property->id)->first();
                if(!$feature){
                    $features[] = new \App\Models\PropertyToPropertyFeature([
                        'property_feature_id' => $feature_id,
                        'value' => $f['value']
                    ]);
                }
                //$feature->save();
        pre($feature, 1);
            }
        }
        $property->save();
        pre($property->id,1);

        //pre($property,1);
        $feature = \App\Models\PropertyFeature::where('name', $featureName)->first();

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
        $address = trim($address);
        $propertyAddress = [

            'address' => $address
        ];
        return json_encode($propertyAddress);
    }

    protected function parsePropertyDescription() //description
    {
        $items = $this->crawler->filter('#m_property_dtl_gendescription');
        $description = null;
        foreach ($items as $description) {
            $description = $description->nodeValue;
        }
        $description = trim($description);
        return $description;
    }

    protected function parseLocationDescription() //LocationDescription
    {
        $items = $this->crawler->filter('#m_property_dtl_locdescription');
        $locationDescription = null;
        foreach ($items as $locationDescription) {
            $description = $locationDescription->nodeValue;
        }
        $locationDescription = trim($locationDescription);
        return $locationDescription;
    }

    protected function parsePropertyPrice()
    {
        $items = $this->crawler->filter('#m_property_dtl_info_hdr_price');
        $price = null;
        foreach ($items as $price) {
            $price = $price->nodeValue;
        }
        $price = trim(preg_replace('/[^\d]/', '', $price));
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

    protected function parsePropertyFeatures() //noname table
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

    protected function parsePropertyDetails()
    {
        $propertyDetails = [];
        $featureColls = $this->crawler->filter('#rptPropertyDetails td');
        //pre(count($featureColls),1);
        if ($featureColls->count()) {
            $featureColls->each(function (Crawler $featureColl) use (&$propertyDetails) {
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
                    $propertyDetails[] = [
                        'name' => $featureName,
                        'value' => $featureValue,
                        'type' => 'PropertyDetails'
                    ];
                }
            });

        }
        //pre($features,1);
        return $propertyDetails;
    }

    protected function parsePropertyBuildingDetails()//building
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


    protected function parsePropertyLandDetails()//land
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

    protected function parsePropertyImages()
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


    protected function parsePropertyRealtors()
    {
        $realtorInfo = [];
        $realtorCells = $this->crawler->filter('#divRealtor .m_property_dtl_realtor_cell');
        if ($realtorCells->count()) {
            $realtorCells->each(function (Crawler $realtorCell) use (&$realtorInfo) {

                $images = [];
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
                        $realtorImages[] = [
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
                        $realtorOfficeImages[] = [
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
                    'realtorOfficePhones' => $realtorOfficePhones,
                    'images' => $images,
                    //  $realtorOfficePicture,
                    //  $realtorPicture


                ];

            });

        }
        //pre($realtorInfo,1);
        return $realtorInfo;
    }


}
