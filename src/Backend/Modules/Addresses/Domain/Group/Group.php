<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Backend\Core\Language\Language;
use Backend\Core\Language\Locale as BackendLocale;
use Backend\Modules\Addresses\Domain\Address\Address;
use Backend\Modules\Addresses\Domain\Group\Exception\GroupNotFoundException;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Common\Locale;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Backend\Core\Engine\Model as BackendModel;
use Frontend\Core\Engine\Navigation;

/**
 * @ORM\Table(name="addresses_groups")
 * @ORM\Entity(repositoryClass="Backend\Modules\Addresses\Domain\Group\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
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
     * @ORM\Column(type="integer")
     */
    private $extraId;

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
    protected $images;

    /**
     * @var ArrayCollection|GroupTranslation[]
     * @ORM\OneToMany(targetEntity="GroupTranslation", mappedBy="group", orphanRemoval=true, cascade={"persist"})
     */
    private $translations;

    /**
     * @var Address
     * @ORM\ManyToMany(targetEntity="Backend\Modules\Addresses\Domain\Address\Address", mappedBy="groups", cascade={"persist"})
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    private $addresses;

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
     * Group constructor.
     * @param $userId
     * @param $sequence
     * @param $extraId
     * @param MediaGroup $images
     */
    public function __construct($userId, $sequence, $extraId, MediaGroup $images) {
        $this->translations = new ArrayCollection();
        $this->userId = $userId;
        $this->sequence = $sequence;
        $this->extraId = $extraId;
        $this->images = $images;
    }

    /**
     * @param GroupTranslation $translation
     */
    public function addTranslation(GroupTranslation $translation)
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

    /**
     * @return \Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem|null
     */
    public function getImage()
    {
        return $this->images->getFirstConnectedMediaItem();
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return Address|ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param Locale $locale
     *
     * @throws GroupNotFoundException when the translation does not exist
     *
     * @return GroupTranslation
     */
    public function getTranslation(Locale $locale)
    {
        if ($this->translations->isEmpty()) {
            throw new GroupNotFoundException();
        }

        $translations = $this->translations->filter(
            function (GroupTranslation $translation) use ($locale) {
                return $translation->getLocale()->equals($locale);
            }
        );

        if ($translations->isEmpty()) {
            throw new GroupNotFoundException();
        }

        return $translations->first();
    }

    /**
     * @return GroupTranslation[]|ArrayCollection
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
     * @ORM\PreRemove()
     */
    public function preRemove() {
        BackendModel::deleteExtraById($this->extraId);
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        $this->updateWidget();
    }

    /**
     * @throws GroupNotFoundException
     * @throws \Backend\Core\Engine\Exception
     * @throws \Exception
     */
    public function update()
    {
        $this->updateWidget();
    }

    /**
     * @return MediaGroup
     */
    public function getImages(): MediaGroup
    {
        return $this->images;
    }

    /**
     * @throws GroupNotFoundException
     * @throws \Backend\Core\Engine\Exception
     * @throws \Exception
     */
    private function updateWidget()
    {
        $editUrl = BackendModel::createURLForAction('EditGroup', 'Addresses', Language::getWorkingLanguage(), array(
            'id' => $this->id
        ));

        $extras = BackendModel::getExtras([$this->extraId]);
        $extra = reset($extras);
        $data = [
            'id' => $this->id,
            'language' => Language::getWorkingLanguage(),
            'edit_url' => $editUrl,
        ];
        if (isset($extra['data'])) {
            $data = $data + (array) $extra['data'];
        }
        $data['extra_label'] = Language::getLabel('Group') . ' - ' . $this->getTranslation(BackendLocale::workingLocale())->getTitle();

        BackendModel::updateExtra($this->extraId, 'data', $data);
    }

    public function getWebUrl() {
        return Navigation::getURLForBlock('Addresses') . '#group-' . $this->getId();
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
     * @return string
     * @throws GroupNotFoundException
     */
    public function __toString()
    {
        $locale = BackendLocale::workingLocale();
        return $this->getTranslation($locale)->getTitle();
    }
}