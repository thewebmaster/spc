<?php

require_once __DIR__.'/../spyc/Spyc.php';

/**
 * This class extracts environment configuration from config.yaml or environment variables
 */
abstract class EnvironmentClass
{
    private static $config;
    private static $is_init;

    public static function init()
    {
        if (self::$is_init) {
            return;
        }
        self::$is_init = true;
        $yaml_file = self::getEnvironment('ENV_CONFIG', 'config.yaml');

        if (file_exists($yaml_file)) {
            self::$config = Spyc::YAMLLoad($yaml_file);
        } else {
            $yaml_file = __DIR__.'/../../../'.$yaml_file;
            if (file_exists($yaml_file)) {
                self::$config = Spyc::YAMLLoad($yaml_file);
            }
        }
    }

    /**
     * Get environment value from a given environment variable
     * @param string $name
     * @param string $default
     * @return string
     */
    protected static function getEnvironment($name, $default = null)
    {
        return self::$config && !empty(self::$config[$name])
            ? self::$config[$name]
            : (!empty(getenv($name)) ? getenv($name) : $default);
    }
}

EnvironmentClass::init();
