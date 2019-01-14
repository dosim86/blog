<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IconElementTypeExtension extends AbstractTypeExtension
{
    public function getExtendedTypes(): iterable
    {
        return [
            ButtonType::class,
            TextType::class,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['icon_before', 'icon_after']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['icon_before'] = $options['icon_before'] ?? null;
        $view->vars['icon_after'] = $options['icon_after'] ?? null;
    }
}