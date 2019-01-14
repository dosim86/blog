<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email'
                ],
                'icon_before' => 'envelope',
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Username'
                ],
                'icon_before' => 'user',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['placeholder' => 'Password', 'class' => 'form-control']],
                'second_options' => ['attr' => ['placeholder' => 'Repeat Password', 'class' => 'form-control']],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('register', SubmitType::class, [
                'label'=> 'Sign Up',
                'attr' => ['class' => 'btn btn-success btn-block'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
