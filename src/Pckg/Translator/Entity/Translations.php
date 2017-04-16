<?php namespace Pckg\Translator\Entity;

use Pckg\Database\Entity;
use Pckg\Translator\Record\Translation;

class Translations extends Entity
{

    protected $record = Translation::class;

    public function boot()
    {
        $this->joinTranslations();
    }

}