<?php namespace Pckg\Translator\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Entity\Extension\Translatable;
use Pckg\Translator\Record\Translation;

class Translations extends Entity
{

    use Translatable;

    protected $record = Translation::class;

    public function boot()
    {
        $this->joinTranslations();
    }

}