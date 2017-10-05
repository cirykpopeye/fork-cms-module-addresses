<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Backend\Core\Language\Locale as BackendLocale;
use Backend\Modules\Addresses\Domain\Group\Group;
use Backend\Modules\Addresses\Exceptions\AddressNotFoundException;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\Search\Engine\Model;
use Common\Locale;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Frontend\Core\Engine\Navigation;

/**
 * @ORM\Table(name="addresses")
 * @ORM\Entity(repositoryClass="Backend\Modules\Addresses\Domain\Address\AddressRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Address
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="user_id")
     */
    private $userId;

    /**
     * @var int
     * @ORm\Column(type="integer")
     */
    private $sequence;

    /**
     * @var AddressImage
     *
     * @ORM\Column(type="addresses_address_image", nullable=true)
     */
    private $image;

    /**
     * @var AddressLogo
     * @ORM\Column(type="addresses_address_logo", nullable=true)
     */
    private $logo;

    /**
     * @var AddressBackground
     * @ORM\Column(type="addresses_address_background", nullable=true)
     */
    private $background;

    /**
     * @var MediaGroup
     *
     * @ORM\OneToOne(
     *      targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup",
     *      cascade="persist",
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="mediaGroupId",
     *      referencedColumnName="id",
     *      onDelete="cascade"
     * )
     */
    protected $group;

    /**
     * @var ArrayCollection|Group
     * @ORM\ManyToMany(targetEntity="Backend\Modules\Addresses\Domain\Group\Group", inversedBy="addresses", cascade={"persist"})
     * @ORM\JoinTable(name="addresses_addresses_groups")
     */
    private $groups;

    /**
     * @var string $lastName
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;
    /**
     * @var string $firstName
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;
    /**
     * @var string $email
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;
    /**
     * @var string $street
     * @ORM\Column(type="string", nullable=true)
     */
    private $street;
    /**
     * @var string $number
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;
    /**
     * @var string $postal
     * @ORM\Column(type="string", nullable=true)
     */
    private $postal;
    /**
     * @var string $city
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;
    /**
     * @var string $country
     * @ORM\Column(type="string", nullable=true)
     */
    private $country;
    /**
     * @var string $telephone
     * @ORM\Column(type="string", nullable=true)
     */
    private $telephone;
    /**
     * @var string $fax
     * @ORM\Column(type="string", nullable=true)
     */
    private $fax;
    /**
     * @var string $btw
     * @ORM\Column(type="string", nullable=true)
     */
    private $btw;
    /**
     * @var string $website
     * @ORM\Column(type="string", nullable=true)
     */
    private $website;
    /**
     * @var string $note
     * @ORM\Column(type="string", nullable=true)
     */
    private $note;

    /**
     * @var float $lng
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;
    /**
     * @var float $lat
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @var string $mapsId
     * @ORM\Column(type="string", nullable=true)
     */
    private $mapsId;

    /**
     * @var string $sliderType
     * @ORM\Column(type="string", nullable=true)
     */
    private $sliderType;

    /**
     * @var ArrayCollection|AddressTranslation[]
     * @ORM\OneToMany(targetEntity="AddressTranslation", mappedBy="address", orphanRemoval=true, cascade={"persist"})
     */
    private $translations;

    /**
     * @var Datetime
     *
     * @ORM\Column(type="datetime", name="created_on")
     */
    private $createdOn;

    /**
     * @var Datetime
     *
     * @ORM\Column(type="datetime", name="edited_on")
     */
    private $editedOn;

    /**
     * Address constructor.
     * @param $userId
     * @param AddressImage $image
     * @param $sequence
     * @param $group
     * @param $groups
     * @param $lastName
     * @param $firstName
     * @param $email
     * @param $street
     * @param $number
     * @param $postal
     * @param $city
     * @param $country
     * @param $fax
     * @param $telephone
     * @param $website
     * @param $note
     * @param $btw
     * @param AddressLogo $logo
     * @param AddressBackground $addressBackground
     * @param $sliderType
     */
    public function __construct($userId, AddressImage $image, $sequence, $group, $groups, $lastName, $firstName, $email, $street, $number, $postal, $city, $country, $fax, $telephone, $website, $note, $btw, AddressLogo $logo, AddressBackground $addressBackground, $sliderType) {
        $this->translations = new ArrayCollection();
        $this->userId = $userId;
        $this->image = $image;
        $this->sequence = $sequence;
        $this->group = $group;
        $this->groups = $groups;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->street = $street;
        $this->number = $number;
        $this->postal = $postal;
        $this->city = $city;
        $this->country = $country;
        $this->fax = $fax;
        $this->btw = $btw;
        $this->telephone = $telephone;
        $this->website = $website;
        $this->note = $note;
        $this->logo = $logo;
        $this->background = $addressBackground;
        $this->sliderType = $sliderType;

        if(empty($this->lng) || empty($this->lat)) {
            $this->setGeoLocations();
        }
    }

    /**
     * @param AddressTranslation $translation
     */
    public function addTranslation(AddressTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            return;
        }

        $this->translations->add($translation);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMapsId(): ?string
    {
        return $this->mapsId;
    }

    /**
     * @param string $mapsId
     */
    public function setMapsId(string $mapsId)
    {
        $this->mapsId = $mapsId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function addGroup(Group $group) {
        if($this->groups->contains($group)) {
            return;
        }
        $this->groups[] = $group;
    }

    public function removeGroup(Group $group) {
        if($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return AddressBackground
     */
    public function getBackground(): AddressBackground
    {
        return $this->background;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @return string
     */
    public function getBtw()
    {
        return $this->btw;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return string
     * @throws AddressNotFoundException
     */
    public function getWebUrl() {
        //-- Fetch address URL
        $addressesUrl = Navigation::getURLForBlock('Addresses');
        return $addressesUrl . '/' . $this->getTranslation(\Frontend\Core\Language\Locale::frontendLanguage())->getMeta()->getUrl();
    }

    /**
     * @return AddressImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return AddressLogo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getSliderType(): ?string
    {
        return $this->sliderType;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     */
    public function setSequence(int $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return Group|ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function setGeoLocations() {
        if (!isset($this->lat) || !isset($this->lng)) {
            $parameters = array(
                $this->street,
                $this->number,
                $this->postal,
                $this->city,
                $this->country
            );

            $geoUrl = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode(implode(',', $parameters));

            //-- Fetch the content
            $geoCodes = json_decode(file_get_contents($geoUrl), true);

            // return coordinates latitude/longitude
            $this->lat = array_key_exists(0, $geoCodes['results']) ? $geoCodes['results'][0]['geometry']['location']['lat'] : null;
            $this->lng = array_key_exists(0, $geoCodes['results']) ? $geoCodes['results'][0]['geometry']['location']['lng'] : null;

        }

        if ($this->translations->count()) {
            $this->updateMapsId();
        }

    }

    public function updateSearchIndex() {
        foreach($this->translations as $translation) {
            Model::saveIndex(
                'Addresses',
                $this->getId(),
                array(
                    'title' => $translation->getTitle(),
                    'group' => $this->getGroups()->first()->getTranslation($translation->getLocale())->getTitle(),
                    'description', $translation->getDescription()
                ),
                $translation->getLocale()
            );
        }
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return MediaGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Locale $locale
     *
     * @throws AddressNotFoundException when the translation does not exist
     * @return AddressTranslation
     */
    public function getTranslation(Locale $locale)
    {
        if ($this->translations->isEmpty()) {
            throw new AddressNotFoundException();
        }

        $translations = $this->translations->filter(
            function (AddressTranslation $translation) use ($locale) {
                return $translation->getLocale()->equals($locale);
            }
        );

        if ($translations->isEmpty()) {
            throw new AddressNotFoundException();
        }

        return $translations->first();
    }

    /**
     * @return AddressTranslation[]|ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @ORM\PostUpdate()
     * @ORM\PostPersist()
     */
    public function uploadImages() {
        $this->image->upload();
        $this->logo->upload();
        $this->background->upload();
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function prepareToUploadImages() {
        $this->image->prepareToUpload();
        $this->logo->prepareToUpload();
        $this->background->prepareToUpload();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeImages() {
        $this->logo->remove();
        $this->image->remove();
        $this->background->remove();
    }

    /**
     * @return DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new DateTime();
    }

    /**
     * @param $image
     * @param PersistentCollection $groups
     * @param MediaGroup $group
     * @param $lastName
     * @param $firstName
     * @param $city
     * @param $postal
     * @param $street
     * @param $number
     * @param $country
     * @param $telephone
     * @param $email
     * @param $fax
     * @param $website
     * @param $btw
     * @param $note
     * @param $logo
     * @param $background
     * @param $sliderType
     */
    public function update($image, PersistentCollection $groups, MediaGroup $group, $lastName, $firstName, $city, $postal, $street, $number, $country, $telephone, $email, $fax, $website, $btw, $note, $logo, $background, $sliderType)
    {
        $this->image = $image;
        $this->logo = $logo;
        $this->background = $background;
        $this->groups = $groups;
        $this->group = $group;

        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->city = $city;
        $this->postal = $postal;
        $this->street= $street;
        $this->number = $number;
        $this->country = $country;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->fax = $fax;
        $this->website = $website;
        $this->btw = $btw;
        $this->note = $note;
        $this->sliderType = $sliderType;


        $this->setGeoLocations();
        $this->updateSearchIndex();
    }

    public function updateMapsId() {
        if (empty ($this->mapsId)) {
            //-- Fetch data
            $url = sprintf(
                'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=%s,%s&radius=500&keyword=%s&key=%s',
                $this->getLat(),
                $this->getLng(),
                urlencode($this->getTranslation(\Backend\Core\Language\Locale::workingLocale())->getTitle()),
                'AIzaSyDbpf1N5BkUXv1-Z-tq7vaal6_hNMiksug'
            );


            $data = json_decode(file_get_contents($url), true);

            if (isset ($data['results']) && !empty ($data['results'])) {
                $this->setMapsId($data['results'][0]['place_id']);
            }
        }

    }

    /**
     * Only use this in the backend
     *
     * @return string
     */
    public function __toString()
    {
        $locale = BackendLocale::workingLocale();
        return $this->getTranslation($locale)->getTitle();
    }
}