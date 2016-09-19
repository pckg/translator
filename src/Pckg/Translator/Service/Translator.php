<?php namespace Pckg\Translator\Service;

class Translator
{

    protected $entities;

    protected $data = [];

    public function __construct()
    {
        foreach ($this->getEntities() as $entity) {
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

    public function getEntities()
    {
        return config('pckg.translator.entities');
    }

}