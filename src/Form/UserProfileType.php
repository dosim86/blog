<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('crop_coords', HiddenType::class, [
                'data' => '0',
                'mapped' => false,
            ])
            ->add('uploadedFile', FileType::class, [
                'mapped' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'filestyle d-none',
                    'style' => 'opacity:1',
                    'onchange' => 'previewLoadedFile()'
                ],
                'label_attr' => ['class' => 'border'],
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png'],
                        'detectCorrupted' => true,
                        'groups' => ['profile'],
                        'maxSize' => '1M',
                        'mimeTypesMessage' => 'V_NOT_VALID_IMAGE',
                        'corruptedMessage' => 'V_CORRUPTED_IMAGE',
                    ])
                ],
            ])
            ->add('aboutMe', TextareaType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('apiKey', null, [
                'required' => true,
                'attr' => ['class' => 'form-control', 'readonly' => true]
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
                'icon_before' => 'save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'data_class' => User::class,
            'validation_groups' => ['profile'],
        ]);
    }
}
