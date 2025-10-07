<?php

namespace App\Helpers;

use JetBrains\PhpStorm\ArrayShape;

class Formatters {
    /**
     * Generate html data string from the given array
     * example input/output:
     * ['data1' => 'data1-val', 'data2' => 'data2-val'] => 'data1="data1-val" data2="data2-val"'
     *
     * @param      $dataArray
     * @param bool $leadingSpace
     *
     * @return string
     */
    public static function stringify_data($dataArray, bool $leadingSpace = false): string {
        $first = true;
        $dataString = '';
        foreach ($dataArray as $name => $value) {
            if ($first) {
                $dataString .= ' ';
                $first = false;
            }
            // check if the array was declared as key => value pair
            if (is_string($name)) {
                $dataString .= 'data-' . $name . '=' . $value . ' ';
            } // otherwise use blank value for the data attribute and use the value as the name
            else {
                $dataString .= 'data-' . $value . ' ';
            }
        }
        if ($leadingSpace) {
            // remove trailing whitespace characters
            $dataString = rtrim($dataString);
        } else {
            // remove leading and trailing whitespace characters
            $dataString = trim($dataString);
        }
        return $dataString;
    }

    /**
     *  Generate html class string from the given array
     *  example input/output:
     * ['class1', 'class2'] => 'class1 class2'
     *
     * @param      $classArray
     * @param bool $leadingSpace
     *
     * @return string
     */
    public static function stringify_classes($classArray, bool $leadingSpace = false): string {
        $first = true;
        $classString = '';
        foreach ($classArray as $class) {
            if ($first) {
                $classString .= ' ';
                $first = false;
            }
            $classString .= $class . ' ';
        }
        if ($leadingSpace) {
            // remove trailing whitespace characters
            $classString = rtrim($classString);
        } else {
            // remove leading and trailing whitespace characters
            $classString = trim($classString);
        }

        return $classString;
    }

    /**
     * Format number as percentage
     *
     * @param $num
     *
     * @return string
     */
    public static function number_as_percentage($num): string {
        return self::trim_trailing_zeroes(sprintf("%.2f", $num * 100)) . '%';
    }

    /**
     * Remove trailing zeroes
     *
     * @param        $num
     * @param string $dec_point
     *
     * @return string
     */
    public static function trim_trailing_zeroes($num, string $dec_point = '.'): string {
        if (str_contains($num, $dec_point)) {
            $num = rtrim($num, '0');
        }
        return rtrim($num, $dec_point) ?: '0';
    }

    /**
     * Format a number into a readable form
     *
     * @param int|float|string $number
     * @param int              $decimals
     * @param string           $dec_point
     * @param string           $thousands_sep
     *
     * @return string
     */
    public static function readable_number_format(int|float|string $number, int $decimals = 2, string $dec_point = '.', string $thousands_sep = ' '): string {
        if (is_string($number) || is_int($number)) {
            $number = floatval($number);
        }
        return self::trim_trailing_zeroes(number_format($number, $decimals, $dec_point, $thousands_sep));
    }

    /**
     * Shortens a number and attaches K, M, B, etc. accordingly
     *
     * @param float|int  $number
     * @param int        $decimals
     * @param string     $dec_point
     * @param string     $thousands_sep
     * @param array|null $divisors
     *
     * @return string
     */
    public static function number_letter_notation(float|int $number, int $decimals = 2, string $dec_point = '.', string $thousands_sep = ' ', ?array $divisors = null): string {
        // Setup default $divisors if not provided
        if (!isset($divisors)) {
            $divisors = [
                pow(1000, 0) => '', // 1000^0 == 1
                pow(1000, 1) => 'k', // Thousand
                pow(1000, 2) => 'M', // Million
                pow(1000, 3) => 'B', // Billion
                pow(1000, 4) => 'T', // Trillion
                pow(1000, 5) => 'Qa', // Quadrillion
                pow(1000, 6) => 'Qi', // Quintillion
            ];
        }

        // Loop through each $divisor and find the
        // lowest amount that matches
        $divisor = 1;
        $shorthand = '';
        foreach ($divisors as $divisor => $shorthand) {
            if (abs($number) < ($divisor * 1000)) {
                // We found a match!
                break;
            }
        }

        // We found our match, or there were no matches.
        // Either way, use the last defined value for $divisor.
        return self::trim_trailing_zeroes(number_format($number / $divisor, $decimals, $dec_point, $thousands_sep), $dec_point) . $shorthand;
    }

    /**
     * Truncate a string to the given max length
     *
     * @param string $string
     * @param int    $length
     * @param string $append
     *
     * @return string
     */
    public static function truncate(string $string, int $length, string $append = "&hellip;"): string {
        return (strlen($string) > $length) ? mb_substr($string, 0, $length - strlen($append)) . $append : $string;
    }

    /**
     * Truncate a string to the given max length, using word wrap.
     *
     * @param string $string
     * @param int    $length
     * @param string $append
     *
     * @return string
     */
    public static function truncate_wordwrap(string $string, int $length, string $append = "&hellip;"): string {
        $string = trim($string);

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = $string[0] . $append;
        }

        return $string;
    }

    public static function to_hyphen_case(string $input): string {
        return self::to_snake_case($input, '-');
    }

    public static function to_snake_case(string $input, string $divider = '_'): string {
        // $input = preg_replace('/[^A-Za-z0-9_]/', '', $input); // remove special characters
        $input = preg_replace('/[.,_\-\/\\\]/', '', $input);                                                       // remove special characters
        $input = str_replace(' ', $divider, $input);                                                               // remove all spaces
        $input = self::clean_string($input);                                                                       // transliterate non-roman characters
        // $input = iconv('UTF-8', 'ASCII//TRANSLIT', $input);                            // transliterate non-roman characters
        $input = preg_replace("/$divider{2,}/", $divider, $input);                                                 // remove multiple underscores
        $input = preg_replace('/([a-zA-Z])(?=[A-Z])/', "$1$divider", $input);                                      // add divider after capital letters
        return strtolower(preg_replace('/(?<!^)[a-z][A-Z][0-9]/', "$divider$0", $input));                          // make lowercase and add underscores
    }

    public static function clean_string(string $text): string {
        $utf8 = [
            '/[áàâãªäåą]/u' => 'a',
            '/[ÁÀÂÃÄÅĄ]/u'  => 'A',
            '/[ÍÌÎÏ]/u'     => 'I',
            '/[íìîï]/u'     => 'i',
            '/[éèêëę]/u'    => 'e',
            '/[ÉÈÊËĘ]/u'    => 'E',
            '/[óòôõºö]/u'   => 'o',
            '/[ÓÒÔÕÖ]/u'    => 'O',
            '/[úùûü]/u'     => 'u',
            '/[ÚÙÛÜ]/u'     => 'U',
            '/[çć]/u'       => 'c',
            '/[ÇĆ]/u'       => 'C',
            '/[ñń]/u'       => 'n',
            '/[ÑŃ]/u'       => 'N',
            '/[ł]/u'        => 'l',
            '/[Ł]/u'        => 'L',
            '/[ś]/u'        => 's',
            '/[Ś]/u'        => 'S',
            '/[źż]/u'       => 'z',
            '/[ŹŻ]/u'       => 'Z',
            '/^_.,/'        => '',
            '/–/'           => '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    => ' ', // Literally a single quote
            '/[“”«»„]/u'    => ' ', // Double quote
            '/ /'           => ' ', // non-breaking space (equiv. to 0x160)
        ];
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }

    public static function trim_string(string $input): string {
        return preg_replace('/\s+/', ' ', trim($input));
    }

    public static function case_to_camel_case(string $input, string $divider = '_', bool $capitalizeFirstCharacter = false): string {
        $str = str_replace(' ', '', ucwords(str_replace($divider, ' ', trim($input))));
        if (empty($str)) {
            return $str;
        }
        if (!$capitalizeFirstCharacter) {
            $str[0] = lcfirst($str[0]);
        }
        return $str;
    }

    public static function snake_case_to_sentence(string $input): string {
        $input = str_replace('_', ' ', $input); // replace underscores with spaces
        return self::mb_ucfirst($input);
    }

    /**
     * Capitalize the first letter of the word regardless of the locale encoding
     *
     * @param $str
     *
     * @return string
     */
    public static function mb_ucfirst($str): string {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }

    public static function sanitize_full_text_search(string $input): string {
        $replacementArr = [
            '/\s+/'    => ' ',
            '/\+/'     => 'plus',
            '/#/'      => 'hash',
            '/-/'      => '',
            '/\n/'     => '',
            '/[(\[{]/' => 'brop',
            '/[)\]}]/' => 'brcl',
            '/\'/'     => 'snq',
            '/"/'      => 'dbq',
        ];
        $cleanString = Formatters::clean_string(htmlspecialchars(mb_strtolower($input)));
        return preg_replace(array_keys($replacementArr), array_values($replacementArr), $cleanString);
    }

    public static function sanitize_phone_number(string $phoneNumber): array|string|null {
        return preg_replace('/\D+/', '', $phoneNumber);
    }

    public static function format_phone_number(string $phoneNumber, string $countryCode): string {
        switch ($countryCode) {
            case '48':
                $regex = "/^(\(?\+?0{0,2}?$countryCode\)?)?[ -]?(?|(?>(\d{3})[ -]?(\d{3})[ -]?(\d{3}))|(?>(\d{3})[ -]?(\d{2})[ -]?(\d{2})))$/";
        }
        if (isset($regex) && preg_match($regex, $phoneNumber, $matches)) {
            $result = '(' . $matches[1] . ')' . ' ' . $matches[2] . '-' . $matches[3] . '-' . $matches[4];
            return $result;
        }
        return $phoneNumber;
    }

    /**
     * Split number to integer and decimal part
     *
     * @param float|int|string $number
     * @param string           $dec_point
     * @param string           $thousands_sep
     * @param bool             $trim_trailing_zeroes
     *
     * @return array
     */
    #[ArrayShape(['integer' => 'int|string', 'decimal' => 'int|mixed|string', 'decimalPointCharacter' => 'string'])]
    public static function split_number_decimal(float|int|string $number, string $dec_point = '.', string $thousands_sep = ' ', bool $trim_trailing_zeroes = true): array {
        $splitArr = explode($dec_point, $number);
        [$int, $dec] = count($splitArr) === 2 ? $splitArr : [$number, 0]; // If the length of the split array is equal to 2, there was a decimal part. Otherwise set the decimal part as 0.

        $output = [
            'integer'               => number_format($int, 0, $dec_point, $thousands_sep),
            'decimal'               => $trim_trailing_zeroes ? rtrim($dec, '0') : $dec,
            'decimalPointCharacter' => $dec_point,
        ];

        //if (empty($int)) {
        //    return $output;
        //}
        //if (empty($dec)) {
        //    return $int;
        //}

        return $output;
    }

    /**
     * Truncate a string to the given max length, stopping at the closest word near the end token.
     *
     * @param $string
     * @param $maxLength
     *
     * @return string
     */
    public static function token_truncate($string, $maxLength): string {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $maxLength) {
                break;
            }
        }

        return implode(array_slice($parts, 0, $last_part));
    }
}
