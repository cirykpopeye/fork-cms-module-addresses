<?php

namespace Backend\Modules\Addresses\Domain\Address;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Addresses\Domain\Address\AddressTranslationRepository")
 * @ORM\Table(name="addresses_lang")
 * @ORM\HasLifecycleCallbacks
 */
class AddressTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Locale
     * @ORM\Column(type="locale", name="language")
     */
    private $locale;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="translations")
     * @ORM\JoinColumn(name="addressId", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="title")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="description", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="summary", nullable=true)
     */
    private $summary;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="hidden", nullable=true)
     */
    private $hidden;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $company;

    /**
     * @var Meta
     * @ORM\OneToOne(targetEntity="\Common\Doctrine\Entity\Meta", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(name="meta_id", referencedColumnName="id")
     */
    private $meta;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actionFrom;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actionTill;
    /**
     * @var string $actionMessage
     * @ORM\Column(type="text", nullable=true)
     */
    private $actionMessage;

    /**
     * @var string $titleShort
     * @ORM\Column(type="string", nullable=true)
     */
    private $titleShort;

    /**
     * @param Locale $locale
     * @param Address $address
     * @param string $title
     * @param string $description
     * @param string $summary
     * @param boolean $hidden
     * @param Meta $meta
     * @param $company
     * @param string $actionMessage
     * @param DateTime $actionFrom
     * @param DateTime $actionTill
     * @param string $titleShort
     */
    public function __construct($locale, $address, $title, $description, $summary, $hidden, $meta, $company, ?string $actionMessage, ?DateTime $actionFrom, ?DateTime $actionTill, ?string $titleShort) {
        $this->locale = $locale;
        $this->address = $address;
        $this->title = $title;
        $this->description = $description;
        $this->summary = $summary;
        $this->hidden = $hidden;
        $this->meta = $meta;
        $this->company = $company;
        $this->actionMessage = $actionMessage;
        $this->actionFrom = $actionFrom;
        $this->actionTill = $actionTill;
        $this->titleShort = $titleShort;

        $this->address->addTranslation($this);
    }

    /**
     * @return null|string
     */
    public function getParagraph(): ?string {
        $start = strpos($this->summary, '<p>');
        $end = strpos($this->summary, '</p>', $start);
        $paragraph = substr($this->summary, $start, $end-$start+4);
        return $paragraph;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company)
    {
        $this->company = $company;
    }

    /**
     * @return DateTime
     */
    public function getActionFrom(): ?DateTime
    {
        return $this->actionFrom;
    }

    /**
     * @param DateTime $actionFrom
     */
    public function setActionFrom(DateTime $actionFrom)
    {
        $this->actionFrom = $actionFrom;
    }

    /**
     * @return DateTime
     */
    public function getActionTill(): ?DateTime
    {
        return $this->actionTill;
    }

    /**
     * @param DateTime $actionTill
     */
    public function setActionTill(DateTime $actionTill)
    {
        $this->actionTill = $actionTill;
    }

    /**
     * @return string
     */
    public function getActionMessage(): string
    {
        return (string) $this->actionMessage;
    }

    /**
     * @param string $actionMessage
     */
    public function setActionMessage(string $actionMessage)
    {
        $this->actionMessage = (string) $actionMessage;
    }

    /**
     * @return string
     */
    public function getTitleShort(): string
    {
        return (string) $this->titleShort;
    }

    /**
     * @param string $titleShort
     */
    public function setTitleShort(string $titleShort)
    {
        $this->titleShort = $titleShort;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getSummary() {
        return $this->summary;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Meta
     */
    public function getMeta() {
        return $this->meta;
    }

    public function hasValidAction() {
        //-- Compare dates
        if (
            !empty ($this->actionMessage) &&
            $this->actionFrom < new DateTime() &&
            $this->actionTill > new DateTime()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $summary
     * @param boolean $hidden
     * @param string $company
     * @param string $actionMessage
     * @param DateTime $actionFrom
     * @param DateTime $actionTill
     * @param string $titleShort
     */
    public function update($title, $description, $summary, $hidden, $company, ?string $actionMessage, ?DateTime $actionFrom, ?DateTime $actionTill, ?string $titleShort)
    {
        $this->title = $title;
        $this->description = $description;
        $this->summary = $summary;
        $this->hidden = $hidden;
        $this->company = $company;
        $this->actionMessage = $actionMessage;
        $this->actionFrom = $actionFrom;
        $this->actionTill = $actionTill;
        $this->titleShort = $titleShort;

    }

    /**
     * @return AddressTranslationDataTransferObject
     */
    public function getDataTransferObject()
    {
        $dataTransferObject = new AddressTranslationDataTransferObject($this->locale, $this->address);
        $dataTransferObject->meta = $this->meta;
        $dataTransferObject->description = $this->description;
        $dataTransferObject->title = $this->title;
        $dataTransferObject->summary = $this->summary;
        $dataTransferObject->hidden = $this->hidden;
        $dataTransferObject->actionMessage = $this->actionMessage;
        $dataTransferObject->actionFrom = $this->actionFrom;
        $dataTransferObject->actionTill = $this->actionTill;
        $dataTransferObject->titleShort = $this->titleShort;

        return $dataTransferObject;
    }
}
