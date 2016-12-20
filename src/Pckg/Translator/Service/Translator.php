<?php namespace Pckg\Translator\Service;

class Translator
{

    protected $entities;

    protected $data = [];

    public function __construct()
    {
        /**
         * @T00D00:
         *         - cache translator globally
         *         - support language parameter
         */
        foreach (config('pckg.translator.entities', []) as $entity) {
            $entity = new $entity;
            $entity->joinTranslations();
            $this->data[] = $entity->all()->keyBy('slug');
        }
    }

    public function get($key, $lang = null)
    {
        foreach ($this->data as $collection) {
            if ($collection->keyExists($key)) {
                return $collection[$key]->value;
            }
        }

        return $key;
    }

}