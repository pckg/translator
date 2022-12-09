<?php

namespace Pckg\Translator\Provider;

use Pckg\Framework\Provider;
use Pckg\Translator\Console\ImportTranslations;
use Pckg\Translator\Service\Translator as TranslatorService;

class Translator extends Provider
{
    public function consoles()
    {
        return [
            ImportTranslations::class,
        ];
    }

    public function viewObjects()
    {
        return [
            '_translator' => TranslatorService::class,
        ];
    }
}
