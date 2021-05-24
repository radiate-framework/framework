<?php

namespace Radiate\Support;

use Closure;
use Parsedown;
use Radiate\Support\Pluralizer;

class Stringable
{
    /**
     * The string
     *
     * @var string
     */
    protected $string;

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    public static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    public static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    public static $studlyCache = [];

    /**
     * Create the string instance
     *
     * @param string $string
     */
    public function __construct(string $string = '')
    {
        $this->string = $string;
    }

    /**
     * Return a new instance
     *
     * @param string $string
     * @return static
     */
    public static function of(string $string)
    {
        return new static($string);
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public function after(string $search)
    {
        if ($search === '') {
            return $this;
        }

        return new static(array_reverse(explode($search, $this->string, 2))[0]);
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public function afterLast(string $search)
    {
        if ($search === '') {
            return $this;
        }

        $position = strrpos($this->string, $search);

        if ($position === false) {
            return $this;
        }

        return new static(substr($this->string, $position + strlen($search)));
    }

    /**
     * Append the given values to the string.
     *
     * @param  string  ...$values
     * @return static
     */
    public function append(string ...$values)
    {
        return new static($this->string . implode('', $values));
    }

    /**
     * Get the trailing name component of the path.
     *
     * @param  string  $suffix
     * @return static
     */
    public function basename(string $suffix = '')
    {
        return new static(basename($this->string, $suffix));
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public function before(string $search)
    {
        if ($search === '') {
            return $this;
        }

        $result = strstr($this->string, $search, true);

        return new static($result === false ? $this->string : $result);
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param  string  $search
     * @return static
     */
    public  function beforeLast(string $search)
    {
        if ($search === '') {
            return $this;
        }

        $pos = mb_strrpos($this->string, $search);

        if ($pos === false) {
            return $this;
        }

        return $this->substr(0, $pos);
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param  string  $from
     * @param  string  $to
     * @return static
     */
    public function between(string $from, string $to)
    {
        if ($from === '' || $to === '') {
            return $this;
        }

        return $this->after($from)->beforeLast($to);
    }

    /**
     * Convert a value to camel case.
     *
     * @return static
     */
    public function camel()
    {
        $key = $this->string;

        if (isset(static::$camelCache[$key])) {
            return new static(static::$camelCache[$key]);
        }

        static::$camelCache[$key] = lcfirst($this->studly($key));

        return new static(static::$camelCache[$key]);
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string|string[]  $needles
     * @return bool
     */
    public function contains($needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($this->string, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string contains all array values.
     *
     * @param  string[]  $needles
     * @return bool
     */
    public function containsAll(array $needles)
    {
        foreach ($needles as $needle) {
            if (!$this->contains($needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the parent directory's path.
     *
     * @param  int  $levels
     * @return static
     */
    public function dirname(int $levels = 1)
    {
        return new static(dirname($this->string, $levels));
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string|string[]  $needles
     * @return bool
     */
    public function endsWith($needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($this->string, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the string is an exact match with the given value.
     *
     * @param  string  $value
     * @return bool
     */
    public function exactly(string $value): bool
    {
        return $this->string === $value;
    }

    /**
     * Explode the string into an array.
     *
     * @param  string  $delimiter
     * @param  int  $limit
     * @return array
     */
    public function explode(string $delimiter, int $limit = PHP_INT_MAX): array
    {
        return explode($delimiter, $this->string, $limit);
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $cap
     * @return static
     */
    public function finish(string $cap)
    {
        return new static(rtrim($this->string, $cap) . $cap);
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string|array  $pattern
     * @return bool
     */
    public function is($pattern): bool
    {
        $patterns = is_array($pattern) ? $pattern : [$pattern];

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if ($pattern === $this->string) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $this->string) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the given string is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->string);
    }

    /**
     * Determine if the given string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
    
    /**
     * Generate a UUID (version 4).
     *
     * @return string
     */
    public function uuid(): string
    {
        return wp_generate_uuid4();   
    }

    /**
     * Determine if a given string is a valid UUID.
     *
     * @return bool
     */
    public function isUuid(): bool
    {
        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $this->string) > 0;
    }

    /**
     * Convert a string to kebab case.
     *
     * @return static
     */
    public function kebab()
    {
        return $this->snake('-');
    }

    /**
     * Return the length of the given string.
     *
     * @param  string|null  $encoding
     * @return int
     */
    public function length(?string $encoding = null): int
    {
        if ($encoding) {
            return mb_strlen($this->string, $encoding);
        }

        return mb_strlen($this->string);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param  int  $limit
     * @param  string  $end
     * @return static
     */
    public  function limit(int $limit = 100, string $end = '...')
    {
        if (mb_strwidth($this->string, 'UTF-8') <= $limit) {
            return $this->string;
        }

        return new static(rtrim(mb_strimwidth($this->string, 0, $limit, '', 'UTF-8')) . $end);
    }

    /**
     * Convert the given string to lower-case.
     *
     * @return static
     */
    public function lower()
    {
        return new static(mb_strtolower($this->string, 'UTF-8'));
    }

    /**
     * Converts Markdown into HTML.
     *
     * @return string
     */
    public function markdown()
    {
        return (new Parsedown)->text($this->string);
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param  string  $pattern
     * @return static|null
     */
    public function match(string $pattern)
    {
        preg_match($pattern, $this->string, $matches);

        if (!$matches) {
            return new static;
        }

        return new static($matches[1] ?? $matches[0]);
    }

    /**
     * Get the string matching the given pattern.
     *
     * @param  string  $pattern
     * @return array
     */
    public function matchAll(string $pattern): array
    {
        preg_match_all($pattern, $this->string, $matches);

        if (empty($matches[0])) {
            return [];
        }

        return $matches[1] ?? $matches[0];
    }

    /**
     * Pad both sides of a string with another.
     *
     * @param  int  $length
     * @param  string  $pad
     * @return static
     */
    public function padBoth(int $length, string $pad = ' ')
    {
        return new static(str_pad($this->string, $length, $pad, STR_PAD_BOTH));
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param  int  $length
     * @param  string  $pad
     * @return static
     */
    public function padLeft(int $length, string $pad = ' ')
    {
        return new static(str_pad($this->string, $length, $pad, STR_PAD_LEFT));
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param  int  $length
     * @param  string  $pad
     * @return static
     */
    public function padRight(int $length, string $pad = ' ')
    {
        return new static(str_pad($this->string, $length, $pad, STR_PAD_RIGHT));
    }

    /**
     * Call the given callback and return a new string.
     *
     * @param callable $callback
     * @return static
     */
    public function pipe(callable $callback)
    {
        return new static(call_user_func($callback, $this));
    }

    /**
     * Get the plural form of an English word.
     *
     * @param  int  $count
     * @return static
     */
    public function plural(int $count = 2)
    {
        return new static(Pluralizer::plural($this->string, $count));
    }

    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param  int  $count
     * @return static
     */
    public function pluralStudly(int $count = 2)
    {
        $parts = preg_split('/(.)(?=[A-Z])/u', $this->string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $lastWord = new static(array_pop($parts));

        return new static(implode('', $parts) . $lastWord->plural($count));
    }

    /**
     * Prepend the given values to the string.
     *
     * @param  array  ...$values
     * @return static
     */
    public function prepend(string ...$values)
    {
        return new static(implode('', $values) . $this->string);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return static
     */
    public  function random(int $length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return new static($string);
    }

    /**
     * Replace the given value in the given string.
     *
     * @param  string|string[]  $search
     * @param  string|string[]  $replace
     * @return static
     */
    public function replace($search, $replace)
    {
        return new static(str_replace($search, $replace, $this->string));
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array  $replace
     * @return static
     */
    public function replaceArray(string $search, array $replace)
    {
        $segments = explode($search, $this->string);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= (array_shift($replace) ?? $search) . $segment;
        }

        return new static($result);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @return static
     */
    public function replaceFirst(string $search, string $replace)
    {
        if ($search === '') {
            return $this;
        }

        $position = strpos($this->string, $search);

        if ($position !== false) {
            return new static(substr_replace($this->string, $replace, $position, strlen($search)));
        }

        return $this;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @return static
     */
    public function replaceLast(string $search, string $replace)
    {
        if ($search === '') {
            return $this;
        }

        $position = strrpos($this->string, $search);

        if ($position !== false) {
            return new static(substr_replace($this->string, $replace, $position, strlen($search)));
        }

        return $this;
    }

    /**
     * Replace the patterns matching the given regular expression.
     *
     * @param  string  $pattern
     * @param  \Closure|string  $replace
     * @param  int  $limit
     * @return static
     */
    public function replaceMatches(string $pattern, $replace, int $limit = -1)
    {
        if ($replace instanceof Closure) {
            return new static(preg_replace_callback($pattern, $replace, $this->string, $limit));
        }

        return new static(preg_replace($pattern, $replace, $this->string, $limit));
    }

    /**
     * Split a string using a regular expression or by length.
     *
     * @param  string|int  $pattern
     * @param  int  $limit
     * @param  int  $flags
     * @return array
     */
    public function split($pattern, int $limit = -1, int $flags = 0): array
    {
        if (filter_var($pattern, FILTER_VALIDATE_INT) !== false) {
            return mb_str_split($this->string, $pattern);
        }

        $segments = preg_split($pattern, $this->string, $limit, $flags);

        return !empty($segments) ? $segments : [];
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string  $prefix
     * @return static
     */
    public  function start(string $prefix)
    {
        return new static($prefix . ltrim($this->string, $prefix));
    }

    /**
     * Convert the given string to upper-case.
     *
     * @return static
     */
    public function upper()
    {
        return new static(mb_strtoupper($this->string, 'UTF-8'));
    }

    /**
     * Convert the given string to title case.
     *
     * @return static
     */
    public function title()
    {
        return new static(mb_convert_case($this->string, MB_CASE_TITLE, 'UTF-8'));
    }

    /**
     * Get the singular form of an English word.
     *
     * @return static
     */
    public function singular()
    {
        return new static(Pluralizer::singular($this->string));
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @return static
     */
    public function slug()
    {
        return new static(sanitize_title_with_dashes(remove_accents($this->string)));
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string  $delimiter
     * @return static
     */
    public function snake(string $delimiter = '_')
    {
        $key = $this->string;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return new static(static::$snakeCache[$key][$delimiter]);
        }

        if (!ctype_lower($key)) {
            $value = preg_replace('/\s+/u', '', ucwords($key));

            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        static::$snakeCache[$key][$delimiter] = $value ?? $this->string;

        return new static(static::$snakeCache[$key][$delimiter]);
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string|string[]  $needles
     * @return bool
     */
    public function startsWith($needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($this->string, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @return static
     */
    public function studly()
    {
        $key = $this->string;

        if (isset(static::$studlyCache[$key])) {
            return new static(static::$studlyCache[$key]);
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $key));

        static::$studlyCache[$key] = str_replace(' ', '', $value);

        return new static(static::$studlyCache[$key]);
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param  int  $start
     * @param  int|null  $length
     * @return static
     */
    public  function substr(int $start, ?int $length = null)
    {
        return new static(mb_substr($this->string, $start, $length, 'UTF-8'));
    }

    /**
     * Returns the number of substring occurrences.
     *
     * @param  string  $needle
     * @param  int  $offset
     * @param  int|null  $length
     * @return int
     */
    public function substrCount(string $needle, int $offset = 0, ?int $length = null): int
    {
        if (!is_null($length)) {
            return substr_count($this->string, $needle, $offset, $length);
        } else {
            return substr_count($this->string, $needle, $offset);
        }
    }

    /**
     * Trim the string of the given characters.
     *
     * @param  string  $characters
     * @return static
     */
    public function trim(?string $characters = " \t\n\r\0\x0B")
    {
        return new static(trim($this->string, $characters));
    }

    /**
     * Left trim the string of the given characters.
     *
     * @param  string  $characters
     * @return static
     */
    public function ltrim(?string $characters = " \t\n\r\0\x0B")
    {
        return new static(ltrim($this->string, $characters));
    }

    /**
     * Right trim the string of the given characters.
     *
     * @param  string  $characters
     * @return static
     */
    public function rtrim(?string $characters = " \t\n\r\0\x0B")
    {
        return new static(rtrim($this->string, $characters));
    }

    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param  callable  $callback
     * @return static
     */
    public function tap(callable $callback)
    {
        $callback($this);

        return $this;
    }

    /**
     * Make a string's first character uppercase.
     *
     * @return static
     */
    public function ucfirst()
    {
        return new static(ucfirst($this->string));
    }

    /**
     * Execute the given callback if the string is empty.
     *
     * @param  callable  $callback
     * @return static
     */
    public function whenEmpty(callable $callback)
    {
        if ($this->isEmpty()) {
            $result = $callback($this);

            return is_null($result) ? $this : $result;
        }

        return $this;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param  int  $words
     * @param  string  $end
     * @return static
     */
    public function words(int $words = 100, string $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $this->string, $matches);

        if (!isset($matches[0]) || mb_strlen($this->string) === mb_strlen($matches[0])) {
            return $this;
        }

        return new static(rtrim($matches[0]) . $end);
    }

    /**
     * Dump the string.
     *
     * @return static
     */
    public function dump()
    {
        var_dump($this->string);

        return $this;
    }

    /**
     * Dump the string and end the script.
     *
     * @return void
     */
    public function dd()
    {
        $this->dump();

        die(1);
    }

    /**
     * Proxy dynamic properties onto methods.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->{$key}();
    }

    /**
     * Get the underlying string
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->string;
    }

    /**
     * Get the underlying string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->string;
    }
}
