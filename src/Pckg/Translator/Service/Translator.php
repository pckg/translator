<?php namespace Pckg\Translator\Service;

class Translator
{

    protected $entities;

    protected $data = [];

    public function boot()
    {
        /**
         * @T00D00:
         *         - cache translator globally
         *         - support language parameter
         */
        foreach (config('pckg.translator.entities', []) as $key => $entity) {
            /**
             * First, create entity.
             */
            $entity = new $entity;

            /**
             * Manually set repository.
             */
            if (is_string($key)) {
                $entity->setRepository(context()->get($key));
            }

            /**
             * Join fallback translation in fallback translation language is different.
             */
            $entity->joinFallbackTranslation();

            /**
             * Get translations from database.
             */
            $translations = $entity->all()->keyBy('slug');

            /**
             * Add translations to request cache.
             */
            $this->data[] = $translations;
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