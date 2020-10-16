<?php

require_once __DIR__.'/EnvironmentClass.php';

/**
 * Class to get environment variables
 */
class Environment extends EnvironmentClass
{
	const APP_ENVIRONMENT   = 'APP_ENVIRONMENT';
	const BASE_URL          = 'BASE_URL';
	const DB_HOST           = 'DB_HOST';
	const DB_USERNAME       = 'DB_USERNAME';
	const DB_PASSWORD       = 'DB_PASSWORD';
    const DB_NAME           = 'DB_NAME';

    private static function getBooleanValue($param_name)
    {
        $yaml_value = strtolower(self::getEnvironment($param_name, 0));
        return ($yaml_value === '1') || ($yaml_value === 'true');
    }

    public static function getBaseURL()
    {
        return self::getEnvironment(self::BASE_URL);
	}
	
	/**
	 * Possible return values:
	 * development
	 * testing
	 * production
	 */
	public static function getMode()
    {
        return self::getEnvironment(self::APP_ENVIRONMENT, 'development');
	}
	
	public static function getDBHost()
    {
        return self::getEnvironment(self::DB_HOST);
	}

	public static function getDBUserName()
    {
        return self::getEnvironment(self::DB_USERNAME);
	}

	public static function getDBPassword()
    {
        return self::getEnvironment(self::DB_PASSWORD);
	}

	public static function getDBName()
    {
        return self::getEnvironment(self::DB_NAME);
    }
}
