# laravel-translation-generator
Public

This package provides an easy way to automatically generate language files in a Laravel project using the Google Translate API. It simplifies the process of translating your application into multiple languages by automating the generation of language files based on your source language files.

[![Tests](https://github.com/proshore/laravel-translation-generator/actions/workflows/test.yml/badge.svg)](https://github.com/proshore/laravel-translation-generator/actions/workflows/test.yml)
[![Analyze](https://github.com/proshore/laravel-translation-generator/actions/workflows/analyse.yml/badge.svg)](https://github.com/proshore/laravel-translation-generator/actions/workflows/analyse.yml)

## Installation

You can install this package via Composer.

```bash
composer require proshore/laravel-translation-generator
```

## Configuration
1. After installing the package, publish the configuration file using the following Artisan command:
```bash
php artisan vendor:publish --tag=translator
```

2. Add your Google Translation Api key in env ```GOOGLE_TRANSLATOR_API_KEY```
3. Or Add the Path to the Service Account Json File.

## Usages
### Auto Generate lang Files
1. Run command ```php artisan proshore:translate```
2. Select Your desired language
3. Wait For it Finish. That's it. 

### Convert text to any language

```Proshore\Translator\Facades\TranslatorClient::translate("Hello Worlds","fr");```

### Convert batch text to any language

```Proshore\Translator\Facades\TranslatorClient::translateBatch(["Hello Worlds", "Good Bye],"fr");```

## Contributers ✨
[<img style="border-radius: 50%; border: 2px solid black; width: 50px; height: 50px; object-fit: cover;" src="https://github.com/kundankarna1994.png" width="60px;"/><br /><sub>](https://github.com/kundankarna1994/)

