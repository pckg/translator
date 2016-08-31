<?php namespace Pckg\Translator\Service;

use Pckg\Translator\Entity\GnpTranslations;
use Pckg\Translator\Entity\Translations;

class Translator
{

    protected $entities;

    protected $data = [];

    public function __construct()
    {
        $this->entities = [
            Translations::class,
            GnpTranslations::class,
        ];

        foreach ($this->entities as $entity) {
            $entity = new $entity;
            $this->data[] = $entity->all()->keyBy('slug');
        }
    }

    public function get($key, $lang)
    {
        foreach ($this->data as $collection) {
            if ($collection->keyExists($key)) {
                return $collection[$key]->content;
            }
        }

        return $key;
    }

}