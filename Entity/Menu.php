<?php

declare(strict_types=1);

namespace Monolith\Module\Menu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Monolith\Bundle\CMSBundle\Entity\Site;
use Smart\CoreBundle\Doctrine\ColumnTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="menus",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name_in_site", columns={"name", "site_id"}),
 *      }
 * )
 *
 * @UniqueEntity(fields={"name", "site"}, message="Menu с таким именем уже используется")
 */
class Menu
{
    use ColumnTrait\Id;
    use ColumnTrait\CreatedAt;
    use ColumnTrait\Description;
    use ColumnTrait\Position;
    use ColumnTrait\FosUser;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $properties;

    /**
     * @var MenuItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    protected $items;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Monolith\Bundle\CMSBundle\Entity\Site")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $site;

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        $this->created_at   = new \DateTime();
        $this->position     = 0;
        $this->description  = null;
        $this->items        = new ArrayCollection();
        $this->name         = '123';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): Menu
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $properties
     *
     * @return $this
     */
    public function setProperties($properties): Menu
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperties(): ?string
    {
        return $this->properties;
    }

    /**
     * @param MenuItem[] $items
     *
     * @return $this
     */
    public function setItems($items): Menu
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function setSite(Site $site): Menu
    {
        $this->site = $site;

        return $this;
    }
}
