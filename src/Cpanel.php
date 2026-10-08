<?php

namespace ZanySoft\Cpanel;

use Config;
use Exception;

class Cpanel extends xmlapi
{
    protected $username = '';

    private const MYSQL_MODULE = 'MysqlFE';
    private const SUBDOMAIN_MODULE = 'SubDomain';
    private const EMAIL_MODULE = 'Email';
    private const ADDON_DOMAIN_MODULE = 'AddonDomain';

    /**
     * All parameters to this function are optional and can be set via the accessor functions or constants
     *
     * @param  string|null  $host  The host to perform queries on
     * @param  string|null  $username  The username to authenticate as
     * @param  string|null  $password  The password to authenticate with
     * @throws Exception
     */
    public function __construct(?string $host = null, ?string $username = null, ?string $password = null)
    {

        $config = Config::get('cpanel');

        $protocol = $config['protocol'] ?? 'https';
        $host = $host ?: ($config['host'] ?? $config['ip']);
        $username = $username ?: $config['username'];
        $password = $password ?: $config['password'];

        if (!$host) {
            throw new Exception('Host not defined.');
        }

        parent::__construct($host, $username, $password);

        $auth_type = $config['auth_type'] ?? 'pass';

        $this->set_auth_type($auth_type);
        if ($auth_type == 'hash') {
            $hash = $config['hash'] ?? '';
            $this->setHashAuth($username, $hash);
        } else {
            $this->setAuth($username, $password);
        }

        $this->setProtocol($protocol);
        $this->setHost($host);
        $this->setPort($config['port']);

        $this->set_debug($config['debug'] ?? 0);
        $this->set_output($config['output'] ?? 'array');
    }

    public static function make(?string $host = null, ?string $username = null, ?string $password = null): self
    {
        return new self($host, $username, $password);
    }

    public function setProtocol(string $protocol): self
    {
        $this->set_protocol($protocol);

        return $this;
    }

    public function setHost(string $host): self
    {
        $this->set_host($host);

        return $this;
    }

    public function setPort(int $port): self
    {
        $this->set_port($port);

        return $this;
    }

    public function setAuth(string $username, string $password): self
    {
        $this->username = $username;

        $this->password_auth($username, $password);

        return $this;
    }

    public function setHashAuth(string $username, string $hash): self
    {
        $this->username = $username;

        $this->hash_auth($username, $hash);

        return $this;
    }


    /**
     * Call an API1 function
     *
     *  This function allows you to call API1 from within the XML-API,  This allowes a user to peform actions
     *  such as adding ftp accounts, etc
     *
     * @param  string  $user  The username of the account to perform API1 actions on
     * @param  string  $module  The module of the API1 call to use
     * @param  string  $function  The function of the API1 call
     * @param  array  $args  The arguments for the API1 function, this should be a non-associative array
     * @return mixed
     */
    public function api1(string $user, string $module, string $function, array $args = []): array
    {

        if (!$user || !$module || !$function) {
            return ['reason' => 'api1 requires username, module and function', 'result' => 0];
        }

        $result = $this->api1_query($user, $module, $function, $args);

        return $this->returnResult($result);
    }

    /**
     * Call an API2 Function
     *
     *  This function allows you to call an API2 function, this is the modern API for cPanel and should be used in preference over
     *  API1 when possible
     *
     * @param  string  $user  The username of the account to perform API2 actions on
     * @param  string  $module  The module of the API2 call to use
     * @param  string  $function  The function of the API2 call
     * @param  array  $args  An associative array containing the arguments for the API2 call
     * @return mixed
     */
    public function api2(string $user, string $module, string $function, array $args = []): array
    {
        if (!$user || !$module || !$function) {
            return ['reason' => 'api2 requires username, module and function', 'result' => 0];
        }

        try {
            $result = $this->api2_query($user, $module, $function, $args);

            return $this->returnResult($result);
        } catch (\Exception $e) {
            return [
                'reason' => $e->getMessage(),
                'result' => null
            ];
        }
    }

    public function createSubdomain(string $subdomain, string $username = '', string $subdomain_dir = '', string $main_domain = ''): array
    {

        $subdomain_dir = $subdomain_dir ?: config('cpanel.subdomain_dir');
        $username = $username ?: $this->username;
        $domain = $this->parseDomain($main_domain ?: config('cpanel.domain'));

        if (!$domain) {
            return ['reason' => 'Please provide a valid main domain', 'result' => 0];
        }

        return $this->api2($username, self::SUBDOMAIN_MODULE, 'addsubdomain', [
            'domain' => $subdomain,
            'rootdomain' => $domain,
            'dir' => $subdomain_dir,
            'disallowdot' => 1
        ]);
    }

    public function removeSubdomain(string $subdomain, string $main_domain = ''): array
    {

        $domain = $this->parseDomain($main_domain ?: config('cpanel.domain'));

        if (!$domain) {
            return ['reason' => 'Please provide a valid main domain', 'result' => 0];
        }

        return $this->api2($this->username, self::SUBDOMAIN_MODULE, 'delsubdomain', [
            'domain' => $subdomain.'.'.$domain
        ]);
    }

    public function createdb(string $databaseName): array
    {

        if (!$databaseName) {
            return ['reason' => 'Database name is required', 'result' => 0];
        }

        $databaseName = $this->fixName($databaseName);

        if ($invalid = $this->isNameInvalid($databaseName, 64)) {
            return ['reason' => $invalid, 'result' => 0];
        }

        return $this->api2($this->username, self::MYSQL_MODULE, 'createdb', ['db' => $databaseName]);
    }

    public function deletedb(string $databaseName): array
    {
        if (!$databaseName) {
            return ['reason' => 'Database name is required', 'result' => 0];
        }

        $databaseName = $this->fixName($databaseName);
        return $this->api2($this->username, self::MYSQL_MODULE, 'deletedb', ['db' => $databaseName]);
    }

    public function checkdbuser(string $databaseUser): array
    {
        if (!$databaseUser) {
            return ['reason' => 'Database username is required', 'result' => 0];
        }

        $databaseUser = $this->fixName($databaseUser);
        return $this->api2($this->username, self::MYSQL_MODULE, 'dbuserexists', ['dbuser' => $databaseUser]);
    }

    public function createdbuser(string $username, string $password): array
    {
        if (!$username || !$password) {
            return ['reason' => 'Database username and password are required', 'result' => 0];
        }

        if ($invalid = $this->isNameInvalid($username, 32, 'username')) {
            return ['reason' => $invalid, 'result' => 0];
        }

        $isInvalid = $this->checkPassword($password);
        if ($isInvalid) {
            return [
                'reason' => $isInvalid[0],
                'errors' => $isInvalid,
                'result' => '0'
            ];
        }

        $username = $this->fixName($username);
        $user = $this->checkdbuser($username);

        if ($user['result'] == 1) {
            return ['reason' => 'Database user '.$username.' already exists.', 'result' => 0];
        }

        return $this->api2($this->username, self::MYSQL_MODULE, 'createdbuser', [
                'dbuser' => $username,
                'password' => $password
            ]
        );
    }

    public function setdbuser(string $databaseName, string $databaseUser, string|array $privileges = ''): array
    {

        if (!$databaseName || !$databaseUser) {
            return ['reason' => 'Database name and username are required', 'result' => 0];
        }

        $databaseName = $this->fixName($databaseName);
        $databaseUser = $this->fixName($databaseUser);

        if (is_array($privileges)) {
            $privileges = implode(',', $privileges);
        }

        return $this->api2($this->username, self::MYSQL_MODULE, 'setdbuserprivileges', [
            'privileges' => $privileges,
            'dbuser' => $databaseUser,
            'db' => $databaseName
        ]);
    }

    public function accountsList(string $search_type = '', string $search = ''): array
    {
        return $this->returnResult($this->listaccts($search_type, $search));
    }

    public function accountDetails(string $username = ''): mixed
    {
        $username = $username ?: $this->username;

        return $this->returnResult($this->accountsummary($username));
    }

    /**
     * @deprecated
     */
    public function accountDetials(string $username = ''): mixed
    {
        return $this->accountDetails($username);
    }

    public function createEmailAccount(string $username, string $password, string $domain, int $quota = 500, int $sendWelcomeEmail = 0): array
    {
        return $this->api2($this->username, self::EMAIL_MODULE, 'addpop', [
                'domain' => $domain,
                'email' => $username,
                'password' => $password,
                'quota' => $quota,
                'send_welcome_email' => $sendWelcomeEmail,
            ]
        );
    }

    public function deleteEmailAccount(string $username, string $domain): array
    {
        return $this->api2($this->username, self::EMAIL_MODULE, 'delpop', [
                'domain' => $domain,
                'email' => $username,
            ]
        );
    }

    /**
     * @param  string  $newdomain
     * @param  string  $subdomain
     * @param  string  $dir
     * @param  string  $username
     * @param  int  $ftp_is_optional
     * @return array
     */
    public function createAddonDomain(string $newdomain, string $subdomain, string $dir = '', string $username = '', int $ftp_is_optional = 1): array
    {
        $dir = $dir ?: config('cpanel.subdomain_dir');

        $username = $username ?: $this->username;

        return $this->api2($username, self::ADDON_DOMAIN_MODULE, 'addaddondomain', [
                'newdomain' => $newdomain,
                'subdomain' => $subdomain,
                'dir' => $dir,
                'ftp_is_optional' => $ftp_is_optional
            ]
        );
    }

    /**
     * @param  string  $addon_domain
     * @param  string  $subdomain  The addon domain's username, an underscore (_), and the addon domain's main domain.
     * @param  string  $username
     * @return array
     */
    public function deleteAddonDomain(string $addon_domain, string $subdomain, string $username = ''): array
    {
        $username = $username ?: $this->username;

        return $this->api2($username, self::ADDON_DOMAIN_MODULE, 'deladdondomain', [
                'domain' => $addon_domain,
                'subdomain' => $subdomain,
            ]
        );
    }

    protected function returnResult($result): mixed
    {
        if (is_bool($result)) {
            return ['reason' => '', 'result' => $result];
        }

        if ($this->get_output() === 'xml') {
            $response = simplexml_load_string($result, null, LIBXML_NOERROR | LIBXML_NOWARNING);
            if ($response) {
                $result = json_decode(json_encode($response), true);
            }
        } elseif ($this->get_output() === 'json') {
            $result = json_decode($result, true);
        } else {
            $result = json_decode(json_encode($result), true);
        }

        if (data_get($result, 'cpanelresult')) {
            $result = data_get($result, 'cpanelresult');
        }

        if (!isset($result['data'])) {
            return $result;
        }

        $data = $result['data'];
        if (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }

        if (is_array($data)) {
            $reason = (string) (is_array($data['reason']) ? implode(', ', $data['reason']) : $data['reason']);
            $resultValue = (string) (is_array($data['result']) ? array_shift($data['result']) : $data['result']);

            $reason = $this->cleanReason($reason);
            return ['reason' => $reason, 'result' => (int) $resultValue];
        }

        $reason = $this->getOperationMessage($result['func'] ?? null, $data);

        return ['reason' => $reason, 'result' => (int) $data];
    }


    protected function parseDomain(string $domain): string
    {
        $parse = parse_url($domain);

        if (isset($parse['host'])) {
            $domain = $parse['host'];
        } elseif (mb_strpos($domain, '/', 2) !== false) {
            $domain = strstr($domain, '/', true);
        }

        $domain = str_replace('www.', '', $domain);

        return (mb_strpos($domain, '.') !== false) ? $domain : '';
    }

    protected function isNameInvalid(string $name, int $maxLength, string $for = 'name'): ?string
    {
        $length = strlen($name);

        if ($this->hasUsernamePrefixed($name)) {
            $length = $length - (strlen($this->username) + 1);
        }

        if ($length < 4) {
            return $maxLength
                ? "Database {$for} must be between 4 and {$maxLength} characters"
                : "Database {$for} must be at least 4 characters";
        }

        if ($maxLength && $length > $maxLength) {
            return "Database {$for} must not exceed {$maxLength} characters";
        }

        return null;
    }

    protected function checkPassword(string $pwd): ?array
    {
        $errors = [];
        if (strlen($pwd) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        if (!preg_match('#[0-9]+#', $pwd)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('#[a-zA-Z]+#', $pwd)) {
            $errors[] = 'Password must contain at least one letter';
        }

        return !empty($errors) ? $errors : null;
    }

    protected function slug(string $title, string $separator = '-'): string
    {
        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip,'/').']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator,'/').'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator,'/').'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    protected function hasUsernamePrefixed(string $name): bool
    {
        return strpos(strtolower($name), strtolower($this->username)) === 0;
    }

    protected function fixName(string $name): string
    {
        return $this->hasUsernamePrefixed($name) ? $name : $this->username.'_'.$name;
    }

    private function cleanReason(string $reason): string
    {
        if (mb_strpos($reason, ')') !== false) {
            $reason = ltrim(strstr($reason, ')'), ') ');
        }
        if (mb_strpos($reason, ' at ') !== false) {
            $reason = trim(strstr($reason, ' at ', true));
        }

        return $reason;
    }

    private function getOperationMessage(?string $function, $status): string
    {
        $messages = [
            'createdb' => 'Database'.($status ? ' ' : ' not ').'created successfully',
            'createdbuser' => 'Database user'.($status ? ' ' : ' not ').'created successfully',
            'dbuserexists' => 'Database user '.($status ? 'exists' : 'does not exist'),
            'addsubdomain' => 'Subdomain '.($status ? 'created successfully' : 'not created'),
            'delsubdomain' => 'Subdomain '.($status ? 'removed successfully' : 'not removed'),
            'setdbuserprivileges' => 'Database user privileges '.($status ? 'set successfully' : 'not set'),
        ];

        return $messages[$function] ?? '';
    }
}
