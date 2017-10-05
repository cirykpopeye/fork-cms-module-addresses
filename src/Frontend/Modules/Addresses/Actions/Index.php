<?php

namespace Frontend\Modules\Addresses\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Locale;

/**
 * This is the index-action
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var AddressRepository $addressRepository
     */
    private $addressRepository;

    /**
     * Execute the extra
     */
    public function execute(): void
    {
        parent::execute();
        //-- Check if index or detail
        $this->getData();
        $this->checkSection();
        $this->parse();
    }

    private function checkSection() {
        if($this->url->getParameter(0)) {
            $this->loadAddress($this->url->getParameter(0));
            return;
        }
        $this->loadAddresses();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        $this->addressRepository = $this->get('addresses.repository.address');
    }

    private function loadAddress($url) {

        $this->addJS('ShowAddress.js');
        /** @var Address $address */
        $address = $this->addressRepository->findByUrl($url);
        $this->breadcrumb->addElement($address->getTranslation(Locale::frontendLanguage())->getTitle());
        $this->loadTemplate($this->getModule() . '/Layout/Templates/Address.html.twig');
        $this->template->assign('address', $address);

        $this->addJSData('lat', $address->getLat());
        $this->addJSData('lng', $address->getLng());

        //-- Define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');
        //-- Add JS
        $this->addJS('https://maps.googleapis.com/maps/api/js?key='.$apikey.'&callback=initMap', true, false);
//        $this->addJS('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', true);
//        $this->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey . '&libraries=places', true, false);

        //-- Add JS
//        $this->addJS('ShowAddresses.js');

        //-- Add CSS
        $this->addCSS('ShowAddresses.css');
        $this->addJSData('addresses', array(
            array(
                'id' => $address->getId(),
                'lat' => $address->getLat(),
                'lng' => $address->getLng(),
                'title' => $address->getTranslation(Locale::frontendLanguage())->getTitle(),
                'url' => $address->getWebUrl(),
                'street' => $address->getStreet(),
                'number' => $address->getStreet(),
                'city' => $address->getCity(),
                'postal' => $address->getPostal()
            )
        ));
    }

    private function loadAddresses() {
        $sortBy = array();
        if ($this->get('fork.settings')->get('Addresses', 'SortBySequence', false)) {
            $sortBy['sequence'] = 'ASC';
        }

        $addresses = $this->addressRepository->findAllForMap();
        //-- Fetch all categories from this repository and assign them
        $this->template->assign('addresses', $addresses);
        $this->loadTemplate();
        //-- Define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');
        //-- Add JS
        $this->addJS('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', true);
        $this->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey . '&libraries=places', true, false);

        //-- Add JS
        $this->addJS('ShowAddresses.js');

        //-- Add CSS
        $this->addCSS('ShowAddresses.css');



        $this->template->assign('addressList', $this->addressRepository->findBy(array(), array('sequence' => 'ASC')));
        $this->template->assignArray(array(
//            'addressList' => $this->addressRepository->findAll(),
            'groups' => $this->get('addresses.repository.group')->findBy([], $sortBy)
        ));
        $this->addJSData('addresses', $addresses);
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        //-- Assign a frontend language, so translations can be retrieved.
        $this->template->assign('language', Locale::frontendLanguage());
    }
}