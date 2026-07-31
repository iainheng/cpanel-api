<?php

namespace ZanySoft\Cpanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Factory.
 *
 * @method static \ZanySoft\Cpanel\Cpanel   api1(string $user, string $module, string $function, array $args = [])
 * @method static \ZanySoft\Cpanel\Cpanel   api2(string $user, string $module, string $function, array $args = [])
 * @method static \ZanySoft\Cpanel\Cpanel   setHost(string $host): \ZanySoft\Cpanel\Cpanel
 * @method static \ZanySoft\Cpanel\Cpanel   setPort(int $port): \ZanySoft\Cpanel\Cpanel
 * @method static \ZanySoft\Cpanel\Cpanel   setAuth(string $username, string $password): \ZanySoft\Cpanel\Cpanel
 * @method static \ZanySoft\Cpanel\Cpanel   setHashAuth(string $username, string $hash): \ZanySoft\Cpanel\Cpanel
 * @method static \ZanySoft\Cpanel\Cpanel   createdb(string $databaseName)
 * @method static \ZanySoft\Cpanel\Cpanel   deletedb(string $databaseName)
 * @method static \ZanySoft\Cpanel\Cpanel   checkdbuser(string $databaseUser)
 * @method static \ZanySoft\Cpanel\Cpanel   createdbuser(string $username, string $password)
 * @method static \ZanySoft\Cpanel\Cpanel   setdbuser(string $databaseName, string $databaseUser, string $privileges = '')
 * @method static \ZanySoft\Cpanel\Cpanel   accountsList(string $search_type = '', string $search = '')
 * @method static \ZanySoft\Cpanel\Cpanel   accountDetails(string $username = '')
 * @method static \ZanySoft\Cpanel\Cpanel   createEmailAccount(string $email, string $password, int $quota = 500, string $main_domain = '')
 * @method static \ZanySoft\Cpanel\Cpanel   createSubdomain(string $subdomain, string $username = '', string $subdomain_dir = '', string $main_domain = '')
 * @method static \ZanySoft\Cpanel\Cpanel   removeSubdomain(string $subdomain, string $main_domain = '')
 */
class Cpanel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cpanel';
    }
}
