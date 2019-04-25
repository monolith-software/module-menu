<?php

namespace Monolith\Module\Menu\Form\Type;

use Monolith\Bundle\CMSBundle\Module\AbstractNodePropertiesFormType;
use Monolith\Module\Menu\Entity\Menu;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NodePropertiesFormType extends AbstractNodePropertiesFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('depth',          IntegerType::class,  ['attr' => ['autofocus' => 'autofocus'], 'required' => false])
            ->add('css_class',      TextType::class,     ['required' => false])
            ->add('current_class',  TextType::class,     ['required' => false])
            ->add('selected_inheritance', CheckboxType::class, ['required' => false])
            ->add('menu_id',        ChoiceType::class,   [
                'choices' => $this->getChoicesByEntity(Menu::class, true),
                'label' => 'Menu',
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_menu_node_properties';
    }
}
