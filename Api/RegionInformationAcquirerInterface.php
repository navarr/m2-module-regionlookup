<?php

namespace Navarr\RegionLookup\Api;

interface RegionInformationAcquirerInterface
{
    public function getRegionInfoById($regionId);

    public function getRegionInfoByCode($countryId, $regionCode);

    public function getRegionInfoByName($countryId, $regionName);
}
