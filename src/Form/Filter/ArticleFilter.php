<?php

namespace App\Form\Filter;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArticleFilter extends AbstractType
{
    const QUERYFOR_TITLE = 0;
    const QUERYFOR_CONTENT = 1;
    const QUERYFOR_BOTH = 2;

    const PERIOD_TODAY = 0;
    const PERIOD_LASTWEEK = 1;
    const PERIOD_LASTMONTH = 2;
    const PERIOD_LASTYEAR = 3;
    const PERIOD_ALLTIME = 4;

    private static $queryFor = [
        'Search in title' => self::QUERYFOR_TITLE,
        'Search in content' => self::QUERYFOR_CONTENT,
        'Search in both' => self::QUERYFOR_BOTH,
    ];

    private static $period = [
        'All time' => self::PERIOD_ALLTIME,
        'Today' => self::PERIOD_TODAY,
        'Last week' => self::PERIOD_LASTWEEK,
        'Last month' => self::PERIOD_LASTMONTH,
        'Last year' => self::PERIOD_LASTYEAR,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Search for...']
            ])
            ->add('queryfor', ChoiceType::class, [
                'label' => false,
                'choices' => self::$queryFor
            ])
            ->add('period', ChoiceType::class, [
                'label' => false,
                'choices' => self::$period
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => false,
                'placeholder' => 'Choose tags...',
            ])
            ->add('category', EntityType::class, [
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category...',
                'attr' => ['class' => 'select2'],
            ])
            ->add('search', SubmitType::class, [
                'attr' => ['class' => 'btn-outline-success btn-block']
            ])
            ->add('reset', SubmitType::class, [
                'attr' => ['class' => 'btn-outline-info btn-block']
            ])
        ;

        $this->addAuthorProperty($builder);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $this->addAuthorProperty($event->getForm(), $data['author']);
        });
    }

    private function addAuthorProperty($builder, $email = '')
    {
        /** @var FormInterface $builder */
        $builder->add('author', EntityType::class, [
            'label' => false,
            'class' => User::class,
            'choice_value' => 'email',
            'choice_label' => 'firstname',
            'placeholder' => 'Choose authors...',
            'query_builder' => function (UserRepository $repository) use ($email) {
                return $repository->createQueryBuilder('u')
                    ->andWhere('u.email = :u_email')
                    ->setParameter('u_email', $email)
                    ->setMaxResults(1);
            }
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required' => false,
        ]);
    }
}
