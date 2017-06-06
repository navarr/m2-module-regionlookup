<?php

namespace Navarr\RegionLookup\Model;

use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Directory\Api\Data\RegionInformationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\PhraseFactory;
use Navarr\RegionLookup\Api\RegionInformationAcquirerInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

class RegionInformationAcquirer implements RegionInformationAcquirerInterface
{
    private $countryAcquirer;
    private $phraseFactory;

    public function __construct(CountryInformationAcquirerInterface $countryAcquirer, PhraseFactory $phraseFactory)
    {
        $this->countryAcquirer = $countryAcquirer;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * NOT YET IMPLEMENTED.  DO NOT USE.
     * @param int $regionId
     * @throws LocalizedException
     */
    public function getRegionInfoById($regionId)
    {
        throw new LocalizedException($this->phrase('Not yet implemented'));
    }

    /**
     * Search for a region by code, given a country
     *
     * @param string $countryId
     * @param string $regionCode
     * @return RegionInformationInterface
     * @throws NoSuchEntityException
     */
    public function getRegionInfoByCode($countryId, $regionCode)
    {
        $country = $this->getCountryInformation($countryId);
        $region = $this->searchRegions(
            $country,
            function (RegionInformationInterface $region) use ($regionCode) {
                return $region->getCode() == $regionCode;
            }
        );
        if ($region === null) {
            throw new NoSuchEntityException($this->phrase("Could not locate region with code %s", $regionCode));
        }
        return $region;
    }

    /**
     * Search for a region by name, given a country
     *
     * @param string $countryId
     * @param string $regionName
     * @return RegionInformationInterface
     * @throws NoSuchEntityException
     */
    public function getRegionInfoByName($countryId, $regionName)
    {
        $country = $this->getCountryInformation($countryId);
        $region = $this->searchRegions(
            $country,
            function (RegionInformationInterface $region) use ($regionName) {
                return $region->getName() == $regionName;
            }
        );
        if ($region === null) {
            throw new NoSuchEntityException($this->phrase("Could not locate region with name %s", $regionName));
        }
        return $region;
    }

    /**
     * @param CountryInformationInterface $country The country to search for regions within
     * @param callable $comparator A function that takes a RegionInformationInterface as a parameter and returns true
     *                             if it matches the desired criteria
     * @return RegionInformationInterface|null
     */
    private function searchRegions(CountryInformationInterface $country, callable $comparator)
    {
        $regions = $country->getAvailableRegions();
        foreach ($regions as $region) {
            if ($comparator($region)) {
                return $region;
            }
        }
        return null;
    }

    /**
     * Retrieve a CountryInformationInterface provided a country ID
     *
     * @param string $countryId
     * @return CountryInformationInterface
     * @throws NoSuchEntityException
     */
    private function getCountryInformation($countryId)
    {
        return $this->countryAcquirer->getCountryInfo($countryId);
    }

    /**
     * Equivalent of __('') in Magento
     * @return Phrase
     */
    private function phrase()
    {
        $arguments = func_get_args();
        $text = array_shift($arguments);

        if (!empty($arguments) && is_array($arguments[0])) {
            $arguments = $arguments[0];
        }

        return $this->phraseFactory->create(['text' => $text, 'arguments' => $arguments]);
    }
}
