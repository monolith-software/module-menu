<?php

namespace Monolith\Module\Menu\Controller;

use Monolith\Bundle\CMSBundle\Annotation\NodePropertiesForm;
use Monolith\Bundle\CMSBundle\Entity\Node;
use Monolith\Bundle\CMSBundle\Module\CacheTrait;
use Monolith\Module\Menu\Entity\Menu;
use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    use CacheTrait;

    /**
     * @param Request $request
     * @param Node    $node
     * @param null    $menu_id
     * @param null    $css_class
     * @param string  $current_class
     * @param int     $depth
     * @param bool    $selected_inheritance
     *
     * @return Response
     *
     * @NodePropertiesForm("NodePropertiesFormType")
     */
    public function indexAction(Request $request, Node $node,
        $menu_id = null,
        $css_class = null,
        $current_class = 'active',
        $depth = 0,
        $selected_inheritance = false
    ): Response
    {
        $cmsSecurity = $this->container->get('cms.security');

        if ($cmsSecurity->isSuperAdmin()) {
            $userGroups = 'ROLE_SUPER_ADMIN';
        } else {
            $userGroups = serialize($cmsSecurity->getUserGroups());
        }

        $current_folder_path = $this->get('cms.context')->getCurrentFolderPath();

        $cache_key = md5('monolith_module.menu'.$current_folder_path.',node_id='.$node->getId().',groups='.$userGroups);

        $menu = $node->isCached() ? $this->getCacheService()->get($cache_key) : null;

        if (null === $menu) {
            // Хак для Menu\RequestVoter
            $request->attributes->set('__selected_inheritance', $selected_inheritance);
            $request->attributes->set('__current_folder_path', $current_folder_path);

            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');

            $menu = $this->renderView('@MenuModule/menu.html.twig', [
                'css_class'     => $css_class,
                'current_class' => $current_class,
                'depth'         => $depth,
                'menu'          => $em->find(Menu::class, $menu_id),
            ]);

            //$menu = $this->get('html.tidy')->prettifyFragment($menu);

            if ($node->isCached()) {
                $this->getCacheService()->set($cache_key, $menu, ['monolith_module.menu', 'folder', 'node_'.$node->getId()]);
            }

            $request->attributes->remove('__selected_inheritance');
            $request->attributes->remove('__current_folder_path');
        }

        $node->addFrontControl('edit')
            ->setTitle('Редактировать меню')
            ->setUri($this->generateUrl('monolith_module.menu.admin_menu', [
                'id' => $menu_id,
            ]));

        return new Response($menu);
    }
}
