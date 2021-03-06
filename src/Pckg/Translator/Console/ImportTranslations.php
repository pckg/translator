<?php

namespace Pckg\Translator\Console;

use Pckg\Framework\Console\Command;
use Pckg\Locale\Lang;
use Pckg\Translator\Entity\Translations;
use Pckg\Translator\Record\Translation;
use Pckg\Translator\Service\Translator;

class ImportTranslations extends Command
{

    protected function configure()
    {
        $this->setName('translator:import')
             ->setDescription('Sync all translations to database');
    }

    public function handle()
    {
        $translatorService = context()->getOrCreate(Translator::class);
        $flatTranslations = $translatorService->getFlatDirTranslations();
        $languages = localeManager()->getLanguages()->keyBy('slug');

        foreach ($flatTranslations as $language => $translations) {
            if (!$languages->hasKey($language)) {
                continue;
            }
            $this->output('Checking language ' . $language);
            runInLocale(function () use ($translations, $language) {
                foreach ($translations as $slug => $translation) {
                    $slugTranslation = (new Translations())->where('slug', $slug)->one();

                    if (!$slugTranslation) {
                        $this->output('Create ' . $language . ':' . $slug);
                        Translation::create(['slug' => $slug, 'value' => $translation, 'language_id' => $language]);
                        continue;
                    }

                    $translationRecord = (new Translations())->setTranslatableLang(new Lang($language))
                                                             ->joinTranslations()
                                                             ->where('slug', $slug)
                                                             ->one();

                    if (!$translationRecord->language_id || in_array($slug, config('pckg.translator.cleanTranslations', []))) {
                        $this->output(($translationRecord->language_id ? 'Overwrite ' : 'Add ') . $language . ':' . $slug);
                        $translationRecord->setAndSave(['value' => $translation, 'language_id' => $language]);
                        continue;
                    }
                }
            }, $language);
        }

        $this->output('Done');
    }
}
