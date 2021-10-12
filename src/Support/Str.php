<?php

namespace Radiate\Support;

/**
 * @method static \Radiate\Support\Stringable after(string $string, string $search) Return the remainder of a string after the first occurrence of a given value.
 * @method static \Radiate\Support\Stringable afterLast(string $string, string $search) Return the remainder of a string after the last occurrence of a given value.
 * @method static \Radiate\Support\Stringable append(string $string, string ...$values) Append the given values to the string.
 * @method static \Radiate\Support\Stringable basename(string $string, string $suffix = '') Get the trailing name component of the path.
 * @method static \Radiate\Support\Stringable before(string $string, string $search) Get the portion of a string before the first occurrence of a given value.
 * @method static \Radiate\Support\Stringable beforeLast(string $string, string $search) Get the portion of a string before the last occurrence of a given value.
 * @method static \Radiate\Support\Stringable between(string $string, string $from, string $to) Get the portion of a string between two given values.
 * @method static \Radiate\Support\Stringable camel(string $string) Convert a value to camel case.
 * @method static bool contains(string $string, string|string[] $needles) Determine if a given string contains a given substring.
 * @method static bool containsAll(string $string, string[] $needles) Determine if a given string contains all array values.
 * @method static \Radiate\Support\Stringable dirname(string $string, int $levels = 1) Get the parent directory's path.
 * @method static bool endsWith(string $string, string|string[] $needles) Determine if a given string ends with a given substring.
 * @method static bool exactly(string $string, string $value) Determine if the string is an exact match with the given value.
 * @method static array explode(string $string, string $delimiter, int $limit = PHP_INT_MAX) Explode the string into an array.
 * @method static \Radiate\Support\Stringable finish(string $string, string $cap) Cap a string with a single instance of a given value.
 * @method static bool is(string $string, string|array $pattern) Determine if a given string matches a given pattern.
 * @method static bool isEmpty(string $string) Determine if the given string is empty.
 * @method static bool isNotEmpty(string $string) Determine if the given string is not empty.
 * @method static bool isUuid(string $string) Determine if a given string is a valid UUID.
 * @method static string uuid() Generate a UUID (version 4).
 * @method static \Radiate\Support\Stringable kebab(string $string) Convert a string to kebab case.
 * @method static int length(string $string, ?string $encoding = null) Return the length of the given string.
 * @method static \Radiate\Support\Stringable limit(string $string, int $limit = 100, string $end = '...') Limit the number of characters in a string.
 * @method static \Radiate\Support\Stringable lower(string $string) Convert the given string to lower-case.
 * @method static \Radiate\Support\Markdown markdown(string $string) Converts Markdown into HTML.
 * @method static \Radiate\Support\Stringable|null match(string $string, string $pattern) Get the string matching the given pattern.
 * @method static array matchAll(string $string, string $pattern) Get the string matching the given pattern.
 * @method static \Radiate\Support\Stringable padBoth(string $string, int $length, string $pad = ' ') Pad both sides of a string with another.
 * @method static \Radiate\Support\Stringable padLeft(string $string, int $length, string $pad = ' ') Pad the left side of a string with another.
 * @method static \Radiate\Support\Stringable padRight(string $string, int $length, string $pad = ' ') Pad the right side of a string with another.
 * @method static \Radiate\Support\Stringable pipe(string $string, callable $callback) Call the given callback and return a new string.
 * @method static \Radiate\Support\Stringable plural(string $string, int $count = 2) Get the plural form of an English word.
 * @method static \Radiate\Support\Stringable pluralStudly(string $string, int $count = 2) Pluralize the last word of an English, studly caps case string.
 * @method static \Radiate\Support\Stringable prepend(string $string, string ...$values) Prepend the given values to the string.
 * @method static \Radiate\Support\Stringable random(string $string, int $length = 16) Generate a more truly "random" alpha-numeric string.
 * @method static \Radiate\Support\Stringable replace(string $string, string|string[] $search, string|string[] $replace) Replace the given value in the given string.
 * @method static \Radiate\Support\Stringable replaceArray(string $string, string $search, array $replace) Replace a given value in the string sequentially with an array.
 * @method static \Radiate\Support\Stringable replaceFirst(string $string, string $search, string $replace) Replace the first occurrence of a given value in the string.
 * @method static \Radiate\Support\Stringable replaceLast(string $string, string $search, string $replace) Replace the last occurrence of a given value in the string.
 * @method static \Radiate\Support\Stringable replaceMatches(string $string, string $pattern, \Closure|string $replace, int $limit = -1) Replace the patterns matching the given regular expression.
 * @method static array split(string $string, string|int $pattern, int $limit = -1, int $flags = 0) Split a string using a regular expression or by length.
 * @method static \Radiate\Support\Stringable start(string $string, string $prefix) Begin a string with a single instance of a given value.
 * @method static \Radiate\Support\Stringable upper(string $string) Convert the given string to upper-case.
 * @method static \Radiate\Support\Stringable title(string $string) Convert the given string to title case.
 * @method static \Radiate\Support\Stringable singular(string $string) Get the singular form of an English word.
 * @method static \Radiate\Support\Stringable slug(string $string) Generate a URL friendly "slug" from a given string.
 * @method static \Radiate\Support\Stringable snake(string $string, string $delimiter = '_') Convert a string to snake case.
 * @method static bool startsWith(string $string, string|string[] $needles) Determine if a given string starts with a given substring.
 * @method static \Radiate\Support\Stringable studly(string $string) Convert a value to studly caps case.
 * @method static \Radiate\Support\Stringable substr(string $string, int $start, ?int $length = null) Returns the portion of string specified by the start and length parameters.
 * @method static int substrCount(string $string, string $needle, int $offset = 0, ?int $length = null) Returns the number of substring occurrences.
 * @method static \Radiate\Support\Stringable trim(string $string, ?string $characters = " \t\n\r\0\x0B") Trim the string of the given characters.
 * @method static \Radiate\Support\Stringable ltrim(string $string, ?string $characters = " \t\n\r\0\x0B") Left trim the string of the given characters.
 * @method static \Radiate\Support\Stringable rtrim(string $string, ?string $characters = " \t\n\r\0\x0B") Right trim the string of the given characters.
 * @method static mixed tap(string $string, callable $callback) Call the given Closure with this instance then return the instance.
 * @method static \Radiate\Support\Stringable ucfirst(string $string) Make a string's first character uppercase.
 * @method static \Radiate\Support\Stringable whenEmpty(string $string, callable $callback) Execute the given callback if the string is empty.
 * @method static \Radiate\Support\Stringable words(string $string, int $words = 100, string $end = '...') Limit the number of words in a string.
 * @method static \Radiate\Support\Stringable dump(string $string) Dump the string.
 * @method static void dd(string $string) Dump the string and end the script.
 * @method static string toString(string $string) Get the underlying string
 *
 * @see \Radiate\Support\Stringable
 */
class Str
{
    /**
     * Make a new instance of stringable
     *
     * @param string $string
     * @return \Radiate\Support\Stringable
     */
    public static function of(string $string)
    {
        return new Stringable($string);
    }

    /**
     * Dynamically call the Stringable class
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters = [])
    {
        $str = (new Stringable(array_shift($parameters) ?? ''))->$method(...$parameters);

        return $str instanceof Stringable ? $str->toString() : $str;
    }
}
