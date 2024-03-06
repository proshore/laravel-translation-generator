<?php

namespace Proshore\Translator;

use Exception;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Support\Arr;
use function Laravel\Prompts\spin;

/**
 *
 */
class TranslatorClient
{

    /**
     * @var TranslateClient
     */
    private TranslateClient $translator;

    /**
     * @param TranslateClient $translator
     */
    public function __construct(TranslateClient $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @throws GoogleException
     * @throws Exception
     */
    public static function make(): TranslatorClient
    {
        $key = config('translator.key');
        $keyFilePath = config('translator.keyFilePath');
        if(! $key && ! $keyFilePath) {
            throw new Exception("Google Translation Api Key or KeyFilePath is required");
        }
        $translator = new TranslateClient([
            'key' => $key,
            'keyFilePath' => $keyFilePath,
        ]);

        return new self($translator);
    }


    /**
     * @param string $query
     * @param string $language
     * @return string
     */
    public function getLangTranslation(string $query, string $language) : string
    {
        $translated = spin(fn () => $this->translator->translate($query, [
            'target' => $language,
        ]), 'Fetching Translations');
        if(! $translated || ! array_key_exists('text', $translated)) {
            return config("translator.default");
        }
        $pattern = "/{{(.*?)}}/";
        $replacement = ":$1";

        return preg_replace($pattern, $replacement, $translated['text']);
    }

    /**
     * @param array<string,string|array<string,string>> $translations
     * @param string $language
     * @return array<string,string>
     */
    public function generateSequentialTranslations(array $translations, string $language): array
    {
        $results = [];
        foreach ($translations as $key => $translation) {
            if (is_array($translation)) {
                $results[$key] = $this->generateSequentialTranslations($translation, $language);
            } else {
                $pattern = '/:(\S+)/';
                $replacement = '{{$1}}';
                $translation = (string) preg_replace($pattern, $replacement, $translation);
                $results[$key] = $this->getLangTranslation($translation, $language);
            }
        }

        return $results; // @phpstan-ignore-line
    }

    /**
     * @param array<string,string> $translated
     * @return string
     */
    public function getContent(array $translated): string
    {
        $content = 'return  ' . var_export($translated, true) . ';' . PHP_EOL;
        $content = str_replace('array (', '[', $content);
        $content = str_replace(')', ']', $content);

        return str_replace(' => ', ' => ', $content);
    }


    /**
     * @return array<string,string>
     * @throws ServiceException
     */
    public function getLocalizedLanguages(): array
    {
        $languages = $this->languages();
        Arr::forget($languages, 'en');

        return $languages;
    }

    /**
     * @return array<string,string>
     * @throws ServiceException
     */
    public function languages(): array
    {
        $languages = $this->translator->localizedLanguages();
        $languages = Arr::mapWithKeys($languages, function ($item, $key) {
            return [$item['name'] => $item['code']];
        });
        return array_flip($languages); // @phpstan-ignore-line
    }


    /**
     * @param string $query
     * @param string $language
     * @return string|null
     * @throws ServiceException
     */
    public function translate(string $query, string $language): ?string
    {
        $translation = $this->translator->translate($query, [
            'target' => $language,
        ]);

        return $translation['text'] ?? '';
    }


    /**
     * @param array<int,string> $queries
     * @param string $language
     * @return array<int,string>
     * @throws ServiceException
     */
    public function translateBatch(array $queries, string $language): array
    {
        $translations = $this->translator->translateBatch($queries, [
            'target' => $language,
        ]);

        return Arr::map($translations, function ($translation) {
            return $translation['text'] ?? "" ;
        });

    }
}
