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
            'url' => $url,
            'address'     => $this->parsePropertyAddress(), //propertyStreetAddress, propertyAddressLocality, propertyAddressRegion, propertyPostalCode, propertyExtra
            'description' => $this->parsePropertyDescription(), //text
            'price'       => $this->parsePropertyPrice(),
            'listingID'   => $this->parsePropertyListingID(),
            'summary'     => $this->parsePropertySummary(), //propertyBeds, propertyBaths, propertySize, propertyType,,,,, like 'features' in realtor
            'overview'    => $this->parsePropertyOverview(), //Property Overview + Special Features tables
            'images'      => $this->parsePropertyImages(),
            'realtors'    => $this->parsePropertyRealtors(), // images, realtorOfficeTitle, realtorPhones, realtorName,

             /* Realtor
            'features' => $this->parsePropertyFeatures(),//noname table (Property Type, Building Type, Title, Land Size, Age Of Building, Parking Type,
            'realtors' => $this->parsePropertyRealtors(),
            'buildingDetails' => $this->parsePropertyBuildingDetails(),//building
            'landDetails' => $this->parsePropertyLandDetails(),//land
             */
        ];


        pre($property, 1);
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

        $address = $propertyStreetAddress . ', ' . $propertyAddressLocality . ', ' . $propertyAddressRegion . ', ' . $propertyPostalCode . ', ' . $propertyExtra;
        $propertyAddress = [
            'propertyStreetAddress' => $propertyStreetAddress,
            'propertyAddressLocality' => $propertyAddressLocality,
            'propertyAddressRegion' => $propertyAddressRegion,
            'propertyPostalCode' => $propertyPostalCode,
            'propertyExtra' => $propertyExtra,
            'address' => $address
        ];
        //pre($propertyAddress, 1);
        return $propertyAddress['address'];
    }

    protected function parsePropertySummary()//TODO переписать - должно само делать массив
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
        if (count($propertySummary)){
            foreach ($propertySummary as $propertySummaryItemKey => $propertySummaryItemValue){
                $propertySummaryItem = [
                    'name' => $propertySummaryItemKey,
                    'value' => $propertySummaryItemValue,
                ];
            }
        }
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

    protected function parsePropertyOverview() //Property Overview+Special Features
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

        $items = $this->crawler->filter('.galleria-thumbnails img');
        //pre($items->count(),1);
        if ($items->count()) {
            $items->each(function (Crawler $image) use (&$realtorInfo) {
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
        $realtorCells = $this->crawler->filter('.box--contact .row.contact');
        //pre($realtorCells->count(),1);
        if ($realtorCells->count()) {
            $realtorCells->each(function (Crawler $realtorCell) use (&$realtorInfo) {

                $images =[];
                $realtorPhones=[];

                $realtorPicture = $realtorCell->filter('.contact-logos img');
                //pre ($realtorPicture->count(), 1);
                if ($realtorPicture->count()) {
                        $filename = null;
                        $url = $realtorPicture->attr('src');
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
                }

                $realtorOfficeTitleEl = $realtorCell->filter('.contact-office_name');
                if ($realtorOfficeTitleEl->count()) {
                    $realtorOfficeTitle = trim($realtorOfficeTitleEl->text());
                }

                $realtorCellPhones = $realtorCell->filter('.contact-phonenumbers dd > a');
                //pre($realtorCellPhones->count(),1);
                if ($realtorCellPhones->count()) {
                    $realtorCellPhones->each(function (Crawler $span) use (&$realtorPhones) {
                        $realtorPhones[] = trim($span->text());
                    });
                }

                $realtorNameEl = $realtorCell->filter('.contact-agent_name > a');
                if ($realtorNameEl->count()) {
                    $realtorName = trim($realtorNameEl->text());
                }

                $realtorInfo[] = [
                    'images' => $images,
                    'realtorOfficeTitle' => $realtorOfficeTitle,
                    'realtorPhones' => $realtorPhones,
                    'realtorName' => $realtorName,
                ];

            });
        }
        //pre($realtorInfo,1);
        return $realtorInfo;
    }
}
