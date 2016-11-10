<?php namespace Pckg\Translator\Service;

class Translator
{

    protected $entities;

    protected $data = [];

    public function __construct()
    {
        foreach ($this->getEntities() as $entity) {
            $entity = new $entity;
            if (method_exists($entity, 'joinTranslations')) {
                $entity->joinTranslations();
            }
            $this->data[] = $entity->all()->keyBy('slug');
        }
    }

    public function get($key, $lang = null)
    {
        foreach ($this->data as $collection) {
            if ($collection->keyExists($key)) {
                return $collection[$key]->content ?? ($collection[$key]->value ?? $key);
            }
        }

        return $key;
    }

    public function getEntities()
    {
        return config('pckg.translator.entities', []);
    }

}