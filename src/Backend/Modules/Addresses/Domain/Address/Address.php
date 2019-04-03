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
use Backend\Core\Engine\Model as BackendModel;

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
     */
    public function __construct($userId, $sequence, $group, $groups, $lastName, $firstName, $email, $street, $number, $postal, $city, $country, $fax, $telephone, $website, $note, $btw) {
        $this->translations = new ArrayCollection();
        $this->userId = $userId;
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
     * @return \Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem|null
     */
    public function getImage() {
        return $this->group->getFirstConnectedMediaItem();
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

    /**
     * @param float $lng
     */
    public function setLng(float $lng): void
    {
        $this->lng = $lng;
    }

    /**
     * @param float $lat
     */
    public function setLat(float $lat): void
    {
        $this->lat = $lat;
    }

    public function updateSearchIndex() {
        foreach($this->translations as $translation) {

            $data = array(
                'title' => $translation->getTitle(),
                'description', $translation->getDescription()
            );

            if ($this->getGroups()->count()) {
                $data['group'] = $this->getGroups()->first()->getTranslation($translation->getLocale())->getTitle();
            }

            Model::saveIndex(
                'Addresses',
                $this->getId(),
                $data,
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
     */
    public function update(PersistentCollection $groups, MediaGroup $group, $lastName, $firstName, $city, $postal, $street, $number, $country, $telephone, $email, $fax, $website, $btw, $note)
    {
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

        $this->updateSearchIndex();
    }

    public function __toString()
    {
        $locale = BackendLocale::workingLocale();
        return $this->getTranslation($locale)->getTitle();
    }
}