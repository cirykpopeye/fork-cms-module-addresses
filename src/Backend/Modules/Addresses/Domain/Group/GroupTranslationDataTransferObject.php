<?php

namespace Backend\Modules\Addresses\Domain\Group;

use Common\Doctrine\Entity\Meta;
use Common\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupTranslationDataTransferObject
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var Group|null
     */
    private $group;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "lbl.FieldIsRequired")
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var boolean
     */
    public $hidden;

    /**
     * @var Meta
     */
    public $meta;

    /**
     * @param Locale $locale
     * @param Group|null $group
     */
    public function __construct(Locale $locale, Group $group = null)
    {
        $this->locale = $locale;
        $this->group = $group;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}