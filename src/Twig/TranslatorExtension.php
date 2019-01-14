<?php

namespace App\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslatorExtension extends AbstractExtension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('transL', [$this, 'transLabel']),
            new TwigFilter('transM', [$this, 'transMessage']),
        ];
    }

    public function transLabel($id, $params = [])
    {
        return $this->translator->trans($id, $params, 'labels');
    }

    public function transMessage($id, $params = [])
    {
        return $this->translator->trans($id, $params, 'messages');
    }
}