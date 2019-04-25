<?php

namespace Monolith\Module\Menu\Form\Type;

use FM\ElfinderBundle\Form\Type\ElFinderType;
use Smart\CoreBundle\Form\TypeResolverTtait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemPropertiesFormType extends AbstractType
{
    use TypeResolverTtait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'properties'  => [],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['properties'] as $name => $opt) {
            if (isset($opt['type'])) {

                if ($opt['type'] == 'file') {
                    $type = ElFinderType::class;
                    $opt['instance'] = 'form';
                    $opt['enable'] = true;
                } else {
                    $type = $this->resolveTypeName($opt['type']);
                }
            }

            if (is_array($opt)) {
                if (isset($opt['type'])) {
                    unset($opt['type']);
                }
            } else {
                $type = $this->resolveTypeName($opt);
                $opt = [];
            }

            if (!isset($opt['required'])) {
                $opt['required'] = false;
            }

            if (!isset($opt['translation_domain'])) {
                $opt['translation_domain'] = false;
            }

            $builder->add($name, $type, $opt);
        }
    }

    public function getBlockPrefix()
    {
        return 'monolith_module_menu_item_properties';
    }
}
