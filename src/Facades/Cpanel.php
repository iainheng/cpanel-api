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
 * @method static \ZanySoft\Cpanel\Cpanel   createEmailAccount(string $username, string $password, string $domain, int $quota = 500, int $sendWelcomeEmail = 0)
 * @method static \ZanySoft\Cpanel\Cpanel   deleteEmailAccount(string $username, string $domain)
 * @method static \ZanySoft\Cpanel\Cpanel   createAddonDomain(string $newdomain, string $subdomain, string $dir = '', string $username = '', int $ftp_is_optional = 1)
 * @method static \ZanySoft\Cpanel\Cpanel   deleteAddonDomain(string $addon_domain, string $subdomain, string $username = '')
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
