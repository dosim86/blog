<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'F_TITLE',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('category', EntityType::class, [
                'label' => 'F_CATEGORY',
                'class' => Category::class,
                'placeholder' => '(no category)',
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'F_CONTENT',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'F_SAVE',
                'attr' => ['class' => 'btn btn-success'],
                'icon_before' => 'save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'translation_domain' => 'forms'
        ]);
    }
}
