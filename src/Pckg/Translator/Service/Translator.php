<?php

namespace Pckg\Translator\Service;

use Pckg\Collection;

class Translator
{

    protected $entities;

    protected $data = [];

    protected $dirs = [];

    protected $booted = false;

    public function boot()
    {
        $this->addEntityTranslations();
        if (implicitDev()) {
            $this->addDirTranslations();
        }
        $this->booted = true;
    }

    public function getFlatDirTranslations()
    {
        $sources = $this->getDirTranslations();
        $flatTranslations = [];
        foreach ($sources as $lang => $source) {
            if (!array_key_exists($lang, $flatTranslations)) {
                $flatTranslations[$lang] = [];
            }
            foreach ($source as $translations) {
                foreach ($translations as $key => $translation) {
                    $this->mergeArrayTranslations($flatTranslations[$lang], $translation, $key);
                }
            }
        }

        return $flatTranslations;
    }

    public function addDirTranslations()
    {
        $language = substr(localeManager()->getCurrent(), 0, 2);
        $flatTranslations = $this->getFlatDirTranslations();

        if (!$flatTranslations || !array_key_exists($language, $flatTranslations)) {
            return $this;
        }

        $this->data[] = new Collection($flatTranslations[$language]);
    }

    private function mergeArrayTranslations(&$flatTranslations, $translations, $prefix)
    {
        if (!is_array($translations)) {
            $translations = [$prefix => $translations];
            $prefix = '';
        }

        foreach ($translations as $key => $translation) {
            if (is_array($translation)) {
                $this->mergeArrayTranslations($flatTranslations, $translation, ($prefix ? $prefix . '.' : '') . $key);
                continue;
            }

            $flatTranslations[($prefix ? $prefix . '.' : '') . $key] = $translation;
        }

        return $this;
    }

    public function getDirTranslations()
    {
        $sources = [];
        foreach ($this->dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $handle = opendir($dir);
            while (false !== ($entry = readdir($handle))) {
                if (!is_file($dir . '/' . $entry)) {
                    continue;
                }

                $sources[str_replace('.php', '', $entry)][] = require $dir . '/' . $entry;
            }
        }

        return $sources;
    }

    public function addEntityTranslations()
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
            $entity = new $entity();

            /**
             * Manually set repository.
             */
            if (is_string($key)) {
                $entity->setRepository(context()->get($key));
            }

            /**
             * Join fallback translation in fallback translation language is different.
             */
            $entity->joinTranslations();
            $entity->joinFallbackTranslation();

            /**
             * Get translations from database.
             */
            $translations = $entity/*->cache('1hour', 'app', Translator::class . ':' . Translations::class)*/
                                   ->all()
                                   ->keyBy('slug');

            /**
             * Add translations to request cache.
             */
            $this->data[] = $translations;
        }
    }

    public function get($key, $lang = null)
    {
        if (!$this->booted) {
            $this->boot();
        }

        $values = [];
        foreach ($this->data as $collection) {
            if ($collection->hasKey($key)) {
                $translation = $collection[$key];

                return is_object($translation) ? $translation->value : $translation;
            }
            if (strpos($key, '(.*)') === false) {
                continue;
            }

            /**
             * Find all translations.
             */
            $key = substr($key, 0, strpos($key, '(.*)'));
            foreach ($collection as $translationKey => $translation) {
                if (strpos($translationKey, $key) !== 0) {
                    continue;
                }

                $values[$translationKey] = is_object($translation) ? $translation->value : $translation;
            }
        }

        if ($values) {
            return $values;
        }

        return $key;
    }

    public function getMultiple($keys = [])
    {
        $values = [];

        foreach ($keys as $key) {
            $translations = $this->get($key);
            if (!is_array($translations)) {
                $translations = [$key => $translations];
            }
            foreach ($translations as $k => $translation) {
                $values[$k] = $translation;
            }
        }

        return $values;
    }

    public function getPublicTranslations()
    {
        return $this->getMultiple(config('pckg.translator.publicTranslations', []));
    }

    public function addDir($dir)
    {
        $this->dirs[] = $dir;

        return $this;
    }
}
