#!/usr/bin/env php
<?php

/**
 * Quick command line utility to convert 
 * a string into PHP setters and getters
 *
 * To specify an accessor use the -f switch
 * to pass a field name to the script in
 * underscore-as-spaces format; e.g:
 *
 * php accessor_generator.php -f name
 * php accessor_generator.php -f date_of_birth
 *
 * Or, pass a quoted, space-delimited 
 * string to create a group of accessors:
 *
 * php accessor_generator.php -f 'first_name last_name age'
 * 
 * Case is ignored. The script will exit if 
 * it encounters a bad character in a field; 
 * e.g: if any field starts with a digit, or 
 * contains a hyphe or other illegal character
 *
 * Output goes to STDOUT so you can pipe it, save 
 * it to a file, or whatever else you like :)
 * 
 * @author Darragh Enright
 */

/**
 * Exit with an error
 *
 * @param string $msg
 */
function error($msg)
{
    echo $msg . PHP_EOL;
    echo 'Exiting script' . PHP_EOL;
    exit(1);
}

// errors

$errorNoOptions = <<<EOL
No fields specified. Please use the '-f' switch
to specify a field or a string of fields.
EOL;

$errorBadField = <<<EOL
The script encountered an illegal 
character in the field list.
EOL;

// accessors template

$accessors = <<<'EOF'
    /**
     * Set %1$s 
     *
     * @param PARAM_TYPE $%2$s
     */
    public function set%3$s($%2$s)
    {
        $this->%2$s = $%2$s;
    }

    /**
     * Get %1$s
     *
     * @return RETURN_TYPE
     */
    public function get%3$s()
    {
        return $this->%2$s;
    }


EOF;

// check -f switch
system('clear');
$opts = getopt('f:');

if (!count($opts)) {
    error($errorNoOptions);
}

// explode options string into an array
$fields = explode(' ', $opts['f']);

$str = '';

// process each field
foreach ($fields as $field) {
    
    $field = trim(strtolower($field), '_');
    
    // check field characters
    if (preg_match('/^[^\d][\w]+$/', $field) === 0) {
        error($errorBadField);
    }
    
    // create field variations for template
    $camelCaseLower = preg_replace_callback('/_([a-z]){1}/', function($match) {
        return strtoupper($match[1]);
    }, $field);
    
    $camelCaseUpper = ucfirst($camelCaseLower);
    $humanReadable  = str_replace('_', ' ', $field);
    
    // create accessor and add
    $str .= sprintf($accessors, $humanReadable, $camelCaseLower, $camelCaseUpper);
}

echo $str;

// done :)
exit(0);
