<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-04-03
 * Time: 08:52
 */

namespace Backend\Modules\Addresses\Domain\Address;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ForkCMS\Utility\Geolocation;

class AddressSubscriber implements EventSubscriber
{
    private $geoLocation;

    public function __construct(Geolocation $geolocation)
    {
        $this->geoLocation = $geolocation;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::prePersist
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setGeoLocations($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setGeoLocations($args);
    }

    private function setGeoLocations(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Address) {
            return;
        }

        $coordinates = $this->geoLocation->getCoordinates(
            $entity->getStreet(),
            $entity->getNumber(),
            $entity->getCity(),
            $entity->getPostal(),
            $entity->getCountry()
        );

        if (
            isset($coordinates['latitude']) &&
            isset($coordinates['longitude'])
        ) {
            $entity->setLat($coordinates['latitude']);
            $entity->setLng($coordinates['longitude']);
        }
    }
}
