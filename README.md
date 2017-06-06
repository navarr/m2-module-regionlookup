# RegionInformationAcquirer Service Contract

## Installation

This module is really only for developers.  You can duplicate it in `app/code` if for some reason you can't use composer (how would that happen??) or you can just 

    composer require navarr/m2-module-regionlookup
    
So far this works with all Magento versions, and the composer version constraints are setup so that it won't install if the modules it uses have a breaking change.

## Usage

There are three methods for use, however only two are currently implemented:

* `getRegionInfoByCode(string $countryId, string $regionCode) : \Magento\Directory\Api\Data\RegionInformationInterface, throws NoSuchEntityException`
* `getRegionInfoByName(string $countryId, string $regionName) : \Magento\Directory\Api\Data\RegionInformationInterface, throws NoSuchEntityException`

`NoSuchEntityException` is thrown when:

* Country ID could not be found
* Region Name/Code could not be found

A `LocalizedException` is thrown if you dare to use `getRegionInfoById(int $regionId)`

## Example

    public function __construct(RegionInformationAcquirerInterface $regionAcquirer)
    {
        $state = $regionAcquirer->getRegionInfoByCode('US', 'OH');
        $state->getId(); // some number
        $state->getName(); // Ohio
        $state->getCode(); // OH
    }
