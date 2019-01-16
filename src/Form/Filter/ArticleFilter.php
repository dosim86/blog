<?php

namespace App\Form\Filter;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleFilter extends AbstractType
{
    const QUERYFOR_TITLE = -1;
    const QUERYFOR_CONTENT = 1;
    const QUERYFOR_BOTH = 2;

    const PERIOD_TODAY = -1;
    const PERIOD_LASTWEEK = 1;
    const PERIOD_LASTMONTH = 2;
    const PERIOD_LASTYEAR = 3;
    const PERIOD_ALLTIME = 4;

    private static $queryFor = [
        'F_SEARCH_IN_TITLE' => self::QUERYFOR_TITLE,
        'F_SEARCH_IN_CONTENT' => self::QUERYFOR_CONTENT,
        'F_SEARCH_IN_BOTH' => self::QUERYFOR_BOTH,
    ];

    private static $period = [
        'F_ALL_TIME' => self::PERIOD_ALLTIME,
        'F_TODAY' => self::PERIOD_TODAY,
        'F_LAST_WEEK' => self::PERIOD_LASTWEEK,
        'F_LAST_MONTH' => self::PERIOD_LASTMONTH,
        'F_LAST_YEAR' => self::PERIOD_LASTYEAR,
    ];

    private $urlGenerator;

    private $manager;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $manager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->urlGenerator->generate('article_list'))
            ->add('query', null, [
                'label' => false,
                'attr' => ['placeholder' => 'F_SEARCH_FOR']
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
                'choices' => $this->getTags(),
            ])
            ->add('category', EntityType::class, [
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'F_CHOOSE_CATEGORY',
                'attr' => ['class' => 'select2'],
                'choices' => $this->getCategories(),
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
            'placeholder' => 'F_CHOOSE_AUTHORS',
            'choices' => $this->getAuthorsByEmail($email),
        ]);
    }

    private function getCategories()
    {
        return $this->manager->getRepository(Category::class)->getAll();
    }

    private function getTags()
    {
        return $this->manager->getRepository(Tag::class)->getAll();
    }

    private function getAuthorsByEmail($email)
    {
        return $this->manager->getRepository(User::class)->getAuthorsByEmail($email);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required' => false,
            'csrf_token_id' => '_api',
            'translation_domain' => 'forms',
        ]);
    }
}
