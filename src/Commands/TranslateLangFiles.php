<?php

namespace Proshore\Translator\Commands;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Core\Exception\ServiceException;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\search;
use Proshore\Translator\TranslatorClient;

class TranslateLangFiles extends Command
{
    protected $signature = "proshore:translate";

    protected $description = "Translate lang files";

    /**
     * @throws ServiceException|GoogleException
     */
    public function handle(): void
    {
        $translator = TranslatorClient::make();
        $langFilesDir = lang_path("en");
        if(! File::isDirectory($langFilesDir)) {
            $this->error("Please publish your translation files");

            return;
        }

        $languages = $translator->getLocalizedLanguages();

        $language = (string) search(
            label: 'Search for the language you want to translate',
            options: fn (string $value) => strlen($value) > 0
                ? Arr::where($languages, function ($name, $code) use ($value) {
                    return str_contains(Str::lower($name), Str::lower($value));
                })
                : [],
            placeholder: 'E.g. Nepali',
            hint: 'Selected language will be translated',
            required: "Language must be selected"
        );


        $files = File::allFiles($langFilesDir);
        $progress = progress(label: 'Translating Languages', steps: count($files));
        $progress->start();
        foreach ($files as $file) {
            $translations = Lang::get(Str::replace(".php", "", $file->getRelativePathname())); //@phpstan-ignore-line

            if (is_array($translations) && Arr::isAssoc($translations)) {
                $translated = $translator->generateSequentialTranslations($translations, $language);
                $langDir = str_replace("/en", "/" . $language, $file->getPath());
                if(! File::isDirectory($langDir)) {
                    File::makeDirectory(path : $langDir, recursive: true);
                }
                $filePath = $langDir . "/" . $file->getFilename();
                $convertedFile = fopen($filePath, "w");
                if($convertedFile) {
                    fwrite($convertedFile, "<?php" . PHP_EOL . PHP_EOL);
                    $content = $translator->getContent($translated);
                    fwrite($convertedFile, $content . PHP_EOL);

                    fclose($convertedFile);
                }

            }
            $progress->advance();
        }
        $progress->finish();
        $this->info("Translation Files generated Successfully");
    }
}
