<?php

namespace App\Twig;

use App\Repository\SeoRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    private $title;
    private $keywords;
    private $description;

    public function __construct(RequestStack $requestStack, SeoRepository $repository)
    {
        if ($request = $requestStack->getCurrentRequest()) {
            $seo = $repository->findOneBy([
                'isDisabled' => false,
                'path' => $request->getPathInfo()
            ]);

            if ($seo) {
                $this->title = $seo->getTitle() ?: null;
                $this->keywords = $seo->getKeywords() ?: null;
                $this->description = $seo->getDescription() ?: null;
            }
        }
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('seo_title', [$this, 'title'], ['is_safe' => ['html']]),
            new TwigFunction('seo_keywords', [$this, 'keywords'], ['is_safe' => ['html']]),
            new TwigFunction('seo_description', [$this, 'description'], ['is_safe' => ['html']]),
        ];
    }

    public function title()
    {
        return $this->title ? '<title>' . $this->title . '</title>' : '';
    }

    public function keywords()
    {
        return $this->keywords ? '<meta name="keywords" content="' . $this->keywords . '">' : '';
    }

    public function description()
    {
        return $this->description ? '<meta name="description" content="' . $this->description . '">' : '';
    }
}