<?php

namespace Monolith\Module\Menu\Form\Tree;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Monolith\Module\Menu\Entity\Menu;
use Monolith\Module\Menu\Entity\MenuItem;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;

class ItemLoader implements EntityLoaderInterface
{
    /** @var EntityRepository */
    private $repo;

    /** @var array */
    protected $result;

    /** @var int */
    protected $level;

    /** @var Menu */
    protected $menu;

    /**
     * @param ObjectManager $em
     * @param null $manager
     * @param null $class
     */
    public function __construct(ObjectManager $em, $manager = null, $class = null)
    {
        $this->repo = $em->getRepository($class);
    }

    /**
     * @param Menu $menu
     *
     * @return $this
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Returns an array of entities that are valid choices in the corresponding choice list.
     *
     * @return array The entities.
     */
    public function getEntities()
    {
        $this->result = [];
        $this->level = 0;

        $this->addChild();

        return $this->result;
    }

    /**
     * @param MenuItem|null $parent
     */
    protected function addChild($parent = null)
    {
        $level = $this->level;
        $ident = '';
        while ($level--) {
            $ident .= '&nbsp;&nbsp;';
        }

        $this->level++;

        $items = $this->repo->findBy([
                'parent_item' => $parent,
                'menu' => $this->menu,
            ],
            ['position' => 'ASC']
        );

        /** @var $item MenuItem */
        foreach ($items as $item) {
            $item->setFormTitle($ident.$item);
            $this->result[] = $item;
            $this->addChild($item);
        }

        $this->level--;
    }

    /**
     * Returns an array of entities matching the given identifiers.
     *
     * @param string $identifier The identifier field of the object. This method
     *                           is not applicable for fields with multiple
     *                           identifiers.
     * @param array $values The values of the identifiers.
     *
     * @return array The entities.
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        return $this->repo->findBy(
            [$identifier => $values]
        );
    }
}
