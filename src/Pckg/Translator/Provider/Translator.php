<?php

namespace Pckg\Translator\Provider;

use Pckg\Framework\Provider;
use Pckg\Translator\Console\ImportTranslations;

class Translator extends Provider
{

    public function consoles()
    {
        return [
            ImportTranslations::class,
        ];
    }
}
