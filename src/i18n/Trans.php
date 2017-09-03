<?php

namespace queasy\i18n;

use queasy\config\ConfigTrait;

class Trans
{

    use ConfigTrait;

    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $lang;
    private $defaultLang;

    private $translations = array();

    private function __construct()
    {
        $this->defaultLang = self::config()->need('default');
        $this->lang = $this->defaultLang;
    }

    public function trans($key, array $vars = null, $lang = null)
    {
        list($file, $section, $sentence) = explode('.', $key);

        $lang = is_null($lang)? $this->get(): $lang;
        $file = str_replace("\\", '-', str_replace('/', '-', $file));

        if (isset($this->translations[$lang])
                && isset($this->translations[$lang][$file])) { // If file is already in cache try to get sentence translation
            $translations = $this->translations[$lang][$file];
            if (isset($translations[$section])
                    && isset($translations[$section][$sentence])) { // If sentence exists just get it
                return $this->subst($translations[$section][$sentence], $vars);
            } elseif ($lang !== $this->defaultLang) { // If not and current language is not default, try to get translation for default language
                return $this->trans($key, $vars, $this->defaultLang); // recursively
            } else { // Else return not translated key
                return $key;
            }
        } else { // No file in cache yet
            $translationPath = sprintf(self::LANG_PATH_TEMPLATE, $lang, $file);
            if (@file_exists($translationPath)) {
                $translations = @parse_ini_file($translationPath, true);
                if (false !== $translations) { // If file read successfully
                    if (!isset($this->translations[$lang])) {
                        $this->translations[$lang] = array();
                    }

                    $this->translations[$lang][$file] = $translations; // Add to cache

                    if (isset($translations[$section])
                            && isset($translations[$section][$sentence])) { // If sentence exists just get it
                        return $this->subst($translations[$section][$sentence], $vars);
                    }
                }
            }
        }

        if ($lang !== $this->defaultLang) { // If current language is not default, try to get translation for default language
            return $this->trans($key, $vars, $this->defaultLang); // recursively
        } else { // Else return not translated key
            return $key;
        }
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getDefault()
    {
        return $this->defaultLang;
    }

    private function subst($translation, $vars)
    {
        if (is_null($vars)) {
            return $translation;
        }

        foreach ($vars as $var => $value) {
            $translation = str_replace(':' . $var, $value, $translation);
        }

        return $translation;
    }

}

