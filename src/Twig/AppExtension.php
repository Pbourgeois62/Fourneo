<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncateFilter']),
        ];
    }

    public function truncateFilter(string $text, int $length = 20, string $etc = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        $truncatedText = mb_substr($text, 0, $length - mb_strlen($etc));

        $truncatedText = rtrim($truncatedText);

        return $truncatedText . $etc;
    }
}
