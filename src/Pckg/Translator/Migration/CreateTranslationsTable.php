<?php

namespace Pckg\Translator\Migration;

use Pckg\Migration\Migration;

class CreateTranslationsTable extends Migration
{

    public function up()
    {
        $translations = $this->table('translations');
        $translations->slug();
        $translations->varchar('title');

        $translationsI18n = $this->translatable('translations');
        $translationsI18n->text('value');

        $this->save();
    }
}
