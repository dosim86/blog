<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RestorePassFilter extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Email',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
                'icon_before' => 'envelope',
            ])
            ->add('restore', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success btn-block']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
