<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Addresses\Domain\Group\GroupTranslationRepository")
 * @ORM\Table(name="addresses_groups_lang")
 * @ORM\HasLifecycleCallbacks
 */
class GroupTranslation
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
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="translations")
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id")
     */
    private $group;

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
     * @var Meta
     * @ORM\OneToOne(targetEntity="\Common\Doctrine\Entity\Meta", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(name="meta_id", referencedColumnName="id")
     */
    private $meta;

    /**
     * @param Locale $locale
     * @param Group $group
     * @param string $title
     * @param string $description
     * @param string $summary
     * @param boolean $hidden
     * @param Meta $meta
     */
    public function __construct($locale, $group, $title, $description, $summary, $hidden, $meta) {
        $this->locale = $locale;
        $this->group = $group;
        $this->title = $title;
        $this->description = $description;
        $this->summary = $summary;
        $this->hidden = $hidden;
        $this->meta = $meta;

        $this->group->addTranslation($this);
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
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
     * @return Meta
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $title
     * @param $description
     * @param $summary
     * @param $hidden
     */
    public function update($title, $description, $summary, $hidden)
    {
        $this->title = $title;
        $this->description = $description;
        $this->summary = $summary;
        $this->hidden = $hidden;
    }

    /**
     * @return GroupTranslationDataTransferObject
     */
    public function getDataTransferObject()
    {
        $dataTransferObject = new GroupTranslationDataTransferObject($this->locale, $this->group);
        $dataTransferObject->meta = $this->meta;
        $dataTransferObject->description = $this->description;
        $dataTransferObject->title = $this->title;
        $dataTransferObject->summary = $this->summary;
        $dataTransferObject->hidden = $this->hidden;

        return $dataTransferObject;
    }
}
