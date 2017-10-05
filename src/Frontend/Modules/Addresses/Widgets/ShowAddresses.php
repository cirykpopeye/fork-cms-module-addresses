<?php

namespace Frontend\Modules\Addresses\Widgets;

use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Address\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Language\Locale;

/**
 * This is the detail widget.
 */
class ShowAddresses extends FrontendBaseWidget
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var ArrayCollection|Address
     */
    private $addresses;

    /**
     * Execute the extra
     */
    public function execute(): void
    {
        parent::execute();
        //-- Get the data
        $this->getData();
        //-- Parse
        $this->parse();
        //-- Load template
        $this->loadTemplate();
    }

    /**
     * Fetch data
     */
    public function getData() {
        //-- Fetches all the addresses
        $this->addressRepository = $this->get('addresses.repository.address');
        $this->addresses = $this->addressRepository->findAllForMap();
    }

    /**
     * Parse to template
     */
    protected function parse() {
        //-- Define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');
        //-- Add JS
        $this->addJS('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', true);
        $this->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey . '&libraries=places', true, false);

        //-- Add CSS
        $this->addCSS('ShowAddresses.css');

        $this->template->assign('addresses', $this->addresses);
        $this->template->assign('addressesList', $this->addressRepository->findAll());
        $this->addJSData('addresses', $this->addresses);
        //-- Assign a frontend language, so translations can be retrieved.
        $this->template->assign('language', Locale::frontendLanguage());

    }
}