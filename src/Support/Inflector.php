<?php

namespace Radiate\Support;

class Inflector
{
    /**
     * Plural inflector rules
     *
     * @var array
     */
    protected static $plural = [
        '/(s)tatus$/i' => '\1tatuses',
        '/(quiz)$/i' => '\1zes',
        '/^(ox)$/i' => '\1\2en',
        '/([m|l])ouse$/i' => '\1ice',
        '/(matr|vert)(ix|ex)$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i' => '\1es',
        '/([^aeiouy]|qu)y$/i' => '\1ies',
        '/(hive)$/i' => '\1s',
        '/(chef)$/i' => '\1s',
        '/(?:([^f])fe|([lre])f)$/i' => '\1\2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '\1a',
        '/(p)erson$/i' => '\1eople',
        '/(?<!u)(m)an$/i' => '\1en',
        '/(c)hild$/i' => '\1hildren',
        '/(buffal|tomat)o$/i' => '\1\2oes',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin)us$/i' => '\1i',
        '/us$/i' => 'uses',
        '/(alias)$/i' => '\1es',
        '/(ax|cris|test)is$/i' => '\1es',
        '/s$/' => 's',
        '/^$/' => '',
        '/$/' => 's',
    ];

    /**
     * Singular inflector rules
     *
     * @var array
     */
    protected static $singular = [
        '/(s)tatuses$/i' => '\1\2tatus',
        '/^(.*)(menu)s$/i' => '\1\2',
        '/(quiz)zes$/i' => '\\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias)(es)*$/i' => '\1',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
        '/([ftw]ax)es/i' => '\1',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/ouses$/' => 'ouse',
        '/([^a])uses$/' => '\1us',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1\2ovie',
        '/(s)eries$/i' => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/(drive)s$/i' => '\1',
        '/([le])ves$/i' => '\1f',
        '/([^rfoa])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(p)eople$/i' => '\1\2erson',
        '/(m)en$/i' => '\1an',
        '/(c)hildren$/i' => '\1\2hild',
        '/(n)ews$/i' => '\1\2ews',
        '/eaus$/' => 'eau',
        '/^(.*us)$/' => '\\1',
        '/s$/i' => '',
    ];

    /**
     * Irregular rules
     *
     * @var array
     */
    protected static $irregular = [
        'atlas' => 'atlases',
        'beef' => 'beefs',
        'brief' => 'briefs',
        'brother' => 'brothers',
        'cafe' => 'cafes',
        'child' => 'children',
        'cookie' => 'cookies',
        'corpus' => 'corpuses',
        'cow' => 'cows',
        'criterion' => 'criteria',
        'ganglion' => 'ganglions',
        'genie' => 'genies',
        'genus' => 'genera',
        'graffito' => 'graffiti',
        'hoof' => 'hoofs',
        'loaf' => 'loaves',
        'man' => 'men',
        'money' => 'monies',
        'mongoose' => 'mongooses',
        'move' => 'moves',
        'mythos' => 'mythoi',
        'niche' => 'niches',
        'numen' => 'numina',
        'occiput' => 'occiputs',
        'octopus' => 'octopuses',
        'opus' => 'opuses',
        'ox' => 'oxen',
        'penis' => 'penises',
        'person' => 'people',
        'sex' => 'sexes',
        'soliloquy' => 'soliloquies',
        'testis' => 'testes',
        'trilby' => 'trilbys',
        'turf' => 'turfs',
        'potato' => 'potatoes',
        'hero' => 'heroes',
        'tooth' => 'teeth',
        'goose' => 'geese',
        'foot' => 'feet',
        'foe' => 'foes',
        'sieve' => 'sieves',
        'cache' => 'caches',
    ];

    /**
     * Words that should not be inflected
     *
     * @var array
     */
    protected static $uninflected = [
        '.*?media', '.*[nrlm]ese', '.*data', '.*deer', '.*fish', '.*measles',
        '.*ois', '.*pox', '.*sheep', 'audio', 'bison', 'cattle', 'chassis',
        'chassis', 'clippers', 'compensation', 'coreopsis', 'debris', 'diabetes',
        'education', 'emoji', 'equipment', 'evidence', 'feedback', 'firmware',
        'furniture', 'gallows', 'gold', 'graffiti', 'hardware', 'headquarters',
        'information', 'innings', 'jedi', 'kin', 'knowledge', 'money', 'moose',
        'news', 'nexus', 'nutrition', 'offspring', 'people', 'plankton',
        'pokemon', 'police', 'proceedings', 'rain', 'recommended', 'related',
        'research', 'rice', 'sea[- ]bass', 'series', 'software', 'species',
        'stadia', 'swine', 'traffic', 'weather', 'wheat',
    ];

    /**
     * Method cache array.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * The initial state of Inflector so reset() works.
     *
     * @var array
     */
    protected static $initialState = [];

    /**
     * Cache inflected values, and return if already available
     *
     * @param string $type Inflection type
     * @param string $key Original value
     * @param string|false $value Inflected value
     * @return string|false Inflected value on cache hit or false on cache miss.
     */
    protected static function cache(string $type, string $key, $value = false)
    {
        $key = '_' . $key;
        $type = '_' . $type;
        if ($value !== false) {
            static::$cache[$type][$key] = $value;

            return $value;
        }
        if (!isset(static::$cache[$type][$key])) {
            return false;
        }

        return static::$cache[$type][$key];
    }

    /**
     * Clears Inflectors inflected value caches. And resets the inflection
     * rules to the initial values.
     *
     * @return void
     */
    public static function reset(): void
    {
        if (empty(static::$initialState)) {
            static::$initialState = get_class_vars(self::class);

            return;
        }
        foreach (static::$initialState as $key => $val) {
            if ($key !== 'initialState') {
                static::${$key} = $val;
            }
        }
    }

    /**
     * Adds custom inflection $rules, of either 'plural', 'singular',
     * 'uninflected' or 'irregular' $type.
     *
     * ### Usage:
     *
     * ```
     * Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
     * Inflector::rules('irregular', ['red' => 'redlings']);
     * Inflector::rules('uninflected', ['dontinflectme']);
     * ```
     *
     * @param string $type The type of inflection, either 'plural', 'singular',
     *    or 'uninflected'.
     * @param array $rules Array of rules to be added.
     * @param bool $reset If true, will unset default inflections for all
     *        new rules that are being defined in $rules.
     * @return void
     */
    public static function rules(string $type, array $rules, bool $reset = false): void
    {
        $var = '_' . $type;

        if ($reset) {
            static::${$var} = $rules;
        } elseif ($type === 'uninflected') {
            static::$uninflected = array_merge(
                $rules,
                static::$uninflected
            );
        } else {
            static::${$var} = $rules + static::${$var};
        }

        static::$cache = [];
    }

    /**
     * Return $word in plural form.
     *
     * @param string $word Word in singular
     * @return string Word in plural
     * @link https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-plural-singular-forms
     */
    public static function pluralize(string $word): string
    {
        if (isset(static::$cache['pluralize'][$word])) {
            return static::$cache['pluralize'][$word];
        }

        if (!isset(static::$cache['irregular']['pluralize'])) {
            $words = array_keys(static::$irregular);
            static::$cache['irregular']['pluralize'] = '/(.*?(?:\\b|_))(' . implode('|', $words) . ')$/i';

            $upperWords = array_map('ucfirst', $words);
            static::$cache['irregular']['upperPluralize'] = '/(.*?(?:\\b|[a-z]))(' . implode('|', $upperWords) . ')$/';
        }

        if (
            preg_match(static::$cache['irregular']['pluralize'], $word, $regs) ||
            preg_match(static::$cache['irregular']['upperPluralize'], $word, $regs)
        ) {
            static::$cache['pluralize'][$word] = $regs[1] . substr($regs[2], 0, 1) .
                substr(static::$irregular[strtolower($regs[2])], 1);

            return static::$cache['pluralize'][$word];
        }

        if (!isset(static::$cache['uninflected'])) {
            static::$cache['uninflected'] = '/^(' . implode('|', static::$uninflected) . ')$/i';
        }

        if (preg_match(static::$cache['uninflected'], $word, $regs)) {
            static::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (static::$plural as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                static::$cache['pluralize'][$word] = preg_replace($rule, $replacement, $word);

                return static::$cache['pluralize'][$word];
            }
        }

        return $word;
    }

    /**
     * Return $word in singular form.
     *
     * @param string $word Word in plural
     * @return string Word in singular
     * @link https://book.cakephp.org/4/en/core-libraries/inflector.html#creating-plural-singular-forms
     */
    public static function singularize(string $word): string
    {
        if (isset(static::$cache['singularize'][$word])) {
            return static::$cache['singularize'][$word];
        }

        if (!isset(static::$cache['irregular']['singular'])) {
            $wordList = array_values(static::$irregular);
            static::$cache['irregular']['singular'] = '/(.*?(?:\\b|_))(' . implode('|', $wordList) . ')$/i';

            $upperWordList = array_map('ucfirst', $wordList);
            static::$cache['irregular']['singularUpper'] = '/(.*?(?:\\b|[a-z]))(' .
                implode('|', $upperWordList) .
                ')$/';
        }

        if (
            preg_match(static::$cache['irregular']['singular'], $word, $regs) ||
            preg_match(static::$cache['irregular']['singularUpper'], $word, $regs)
        ) {
            $suffix = array_search(strtolower($regs[2]), static::$irregular, true);
            $suffix = $suffix ? substr($suffix, 1) : '';
            static::$cache['singularize'][$word] = $regs[1] . substr($regs[2], 0, 1) . $suffix;

            return static::$cache['singularize'][$word];
        }

        if (!isset(static::$cache['uninflected'])) {
            static::$cache['uninflected'] = '/^(' . implode('|', static::$uninflected) . ')$/i';
        }

        if (preg_match(static::$cache['uninflected'], $word, $regs)) {
            static::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (static::$singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                static::$cache['singularize'][$word] = preg_replace($rule, $replacement, $word);

                return static::$cache['singularize'][$word];
            }
        }
        static::$cache['singularize'][$word] = $word;

        return $word;
    }
}
