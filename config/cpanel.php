<?php

return [

    /*
    *--------------------------------------------------------------------------
    * Domain name without www and http:// or https://
    *--------------------------------------------------------------------------
    */

    'domain' => '',

    /*
    *--------------------------------------------------------------------------
    * Folder name for subdomains with path.
    *--------------------------------------------------------------------------
    */

    'subdomain_dir' => 'subdomain',

    /*
    *--------------------------------------------------------------------------
    * Cpanel host or  IP
    *--------------------------------------------------------------------------
    * Set the port to connect to
    */

    'host' => '127.0.0.1',

    /*
    *--------------------------------------------------------------------------
    * Set the protocol to use to query
    *--------------------------------------------------------------------------
    * This will allow you to set the protocol to query cpsrvd with.  The only to acceptable values
    * to be passed to this function are 'http' or 'https'.  Anything else will cause the class to throw
    * an Exception.
    *
    */
    'protocol' => 'https',

    /*
    *--------------------------------------------------------------------------
    * Cpanel port
    *--------------------------------------------------------------------------
    * Set the port to connect to
    * This will allow a user to define which port needs to be connected to.
    * The default port set within the class is 2087 (WHM-SSL) however other ports are optional
    * this function will automatically set the protocol to http if the port is equal to:
    *    - 2082
    *    - 2086
    *    - 2095
    *    - 80
    *
    */

    'port' => '2083',

    /*
    *--------------------------------------------------------------------------
    * Cpanel User Name
    *--------------------------------------------------------------------------
    * Set the username to be used for authentication
    */

    'username' => 'root',


    /*
    *--------------------------------------------------------------------------
    * Set the auth type
    *--------------------------------------------------------------------------
    *
    * The only accepted values are "hash", "pass" and "token". Any other value will cause an exception to be thrown.
    */

    'auth_type' => 'pass',

    /*
    *--------------------------------------------------------------------------
    * Set the password used for authentication. If you set auth_type to pass
    *--------------------------------------------------------------------------
    */

    'password' => '',

    /*
    *--------------------------------------------------------------------------
    * Set the hash used for authentication. If you set auth_type to hash
    *--------------------------------------------------------------------------
    */

    'hash' => '',

    /*
    *--------------------------------------------------------------------------
    * Set the cPanel API token used for authentication. If you set auth_type to token
    *--------------------------------------------------------------------------
    * Create the token in cPanel under Security > Manage API Tokens.
    */

    'token' => '',

    /*
    *--------------------------------------------------------------------------
    * Debug Mode
    *--------------------------------------------------------------------------
    * Enabling this option will cause this script to print debug information such as
    * the queries made, the response XML/JSON and other such pertinent information.
    */

    'debug' => false,

];
