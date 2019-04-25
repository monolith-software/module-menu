<?php

namespace Monolith\Module\Menu\Form\Type;

use Monolith\Bundle\CMSBundle\Form\Tree\FolderTreeType;
use Monolith\Module\Menu\Entity\Menu;
use Monolith\Module\Menu\Entity\MenuItem;
use Monolith\Module\Menu\Form\Tree\ItemTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class ItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $menu = null;

        if ($options['data'] instanceof MenuItem) {
            $menu = $options['data']->getMenu();
        }

        if ($options['data'] instanceof Menu) {
            $menu = $options['data'];
        }

        $builder
            ->add('is_active')
            ->add('parent_item', ItemTreeType::class, [
                'menu' => $menu,
                'choice_translation_domain' => false,
            ])
            ->add('folder', FolderTreeType::class, [
                'required' => false,
                'only_active' => true,
                'choice_translation_domain' => false,
            ])
            ->add('title',  null, ['attr' => ['autofocus' => 'autofocus']])
            ->add('url')
            ->add('description')
            ->add('position')
            ->add('open_in_new_window')
        ;

        if ($menu) {
            $properties = Yaml::parse($menu->getProperties());

            if (is_array($properties)) {
                $builder->add(
                    $builder->create('properties', ItemPropertiesFormType::class, [
                        'required' => false,
                        'properties' => $properties,
                    ])
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_menu_item';
    }
}
