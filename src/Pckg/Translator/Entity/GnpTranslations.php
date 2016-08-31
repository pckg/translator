<?php namespace Pckg\Translator\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Repository;

class GnpTranslations extends Entity
{

    protected $repositoryName = Repository::class . '.gnp';

    protected $table = 'translations';

}