<?php namespace Pckg\Translator\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Entity\Extension\Translatable;

class Translations extends Entity
{

    use Translatable;

    public function boot()
    {
        $this->joinTranslations();
    }

}