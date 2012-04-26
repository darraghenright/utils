<?php

// @TODO: better errors for connect() - mysql_errors? possible without warnings?
// @TODO: parse_ini_file returns false on parse error. handle this?
// @TODO: validate all params are supplied for defined connections?
// @TODO: implement PDO and maintain persistent connections

/** 
 * Database Manager
 *
 * Manage connection parameters and 
 * optionally connect to database using
 * mysql_connect() and mysql_select_db()
 *
 * @author Darragh Enright <darraghenright@gmail.com>
 *
 * Example usage:
 * ==============
 *
 * Create an instance:
 * -------------------
 *
 * $dm = new DatabaseManager('/path/to/db-params.ini');
 * 
 * Connect to a database/schema:
 * -----------------------------
 *
 * if ($dm->has('dev_server')) {
 *     $dm->connect('dev_server', 'my_schema');
 * }
 *
 * Or just grab params as an object and use it as you see fit:
 * -----------------------------------------------------------
 *
 * $params = $dm->get('dev_server');
 * $dsn = sprintf('mysql:host=%s;dbname=%s', $params->host, $schema);
 * $db = new PDO($dsn, $params->user, $params->pass);
 *
 * Example ini file connections:
 * =============================
 *
 * @see: http://php.net/manual/en/function.parse-ini-file.php
 *
 * [localhost]
 * host = locahost 
 * user = user
 * pass = xxxx
 *
 * [test_server]
 * host = test.domain.com 
 * user = user
 * pass = xxxx
 *
 * etc.
 */
class DatabaseManager
{
    /**
     * An array of keyed connection param values
     *
     * @var array $connections
     */
    private $connections = array();
    
    /**
     * Parse params file and assign connections
     *
     * @param string $params path to params file
     */
    public function __construct($params)
    {
        $this->connections = $this->parseParams($params);
    }
    
    /**
     * Check if a connection exists
     *
     * @param  string $name
     * @return boolean
     */    
    public function has($key)
    {
        return array_key_exists($key, $this->connections);
    }
    
    /**
     * Get existing connection or trigger an exception
     *
     * @param  string $key
     * @return stdClass
     * @throws Exception
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new Exception(sprintf('Exception: connection key "%s" not found', $key));
        }
        
        return (object) $this->connections[$key]
    }
    
    /**
     * Retrieve an array of all available connection keys
     * 
     * @return array
     */
    public function getAvailableConnectionKeys()
    {
        return array_keys($this->connections);
    }
    
    /**
     * Connect to a specified connection/schema
     *
     * @param  string $key
     * @param  string $schema
     * @throws Exception
     */
    public function connect($key, $schema)
    {
        $params = $this->get($key);
        
        if (!$db = mysql_connect($params->host, $params->user, $params->pass)) {
            $errorConnect = sprintf('Exception: cannot create connection "%s".', $key);
            throw new Exception($errorConnect);
        }
        
        if (!mysql_select_db($schema, $db)) {
            $errorSchema  = sprintf('Exception: cannot select schema "%s".', $schema);
            throw new Exception($errorSchema);
        }
    }
        
    /**
     * Validate, parse and assign params ini file
     *
     * @param  string $params
     * @return array
     */
    protected function parseParams($params)
    {
        $this->isValidPath($params); 
        
        return parse_ini_file($params, true);
    }
    
    /**
     * Check the provided params ini path is a valid, readable file
     *
     * @param  string $file 
     * @throws Exception
     */
    protected function isValidPath($file)
    {
        $error = sprintf('Exception: "%s" is not a file or is not readable.', $file);
        
        if (!is_file($file) || !is_readable($file)) {
            throw new Exception($error);
        }
    }
}

