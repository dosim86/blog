<?php

namespace App\Twig;

use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonExtension extends AbstractExtension
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('json', [$this, 'json'], ['is_safe' => ['html']]),
        ];
    }

    public function json($data, $group = 'frontend')
    {
        return $this->serializer->serialize($data, 'json', ['groups' => $group]);
    }
}