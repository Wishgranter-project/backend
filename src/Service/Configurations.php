<?php

namespace WishgranterProject\Backend\Service;

use WishgranterProject\Backend\Helper\Singleton;

/**
 * Very basic class to manage configuration.
 */
class Configurations extends Singleton
{
    /**
     * Retrieves a setting.
     *
     * @param string $key
     *   The setting to retrieve.
     * @param mixed $default
     *   The value to return in case $get does not exist.
     *
     * @return mixed
     *   The setting's value.
     */
    public function get(string $key, $default = null)
    {
        $config = $this->loadConfig();

        return isset($config[$key])
            ? $config[$key]
            : $default;
    }

    /**
     * Updates/inserts a setting.
     *
     * @param string $key
     *   The setting.
     * @param mixed $value
     *   The value to set.
     */
    public function set(string $key, $value): void
    {
        $config = $this->loadConfig();

        $config[$key] = $value;

        $this->saveConfig($config);
    }

    /**
     * Loads configuration into memory.
     *
     * @return array
     *   The configuration object.
     */
    protected function loadConfig(): array
    {
        $file = ROOT_DIR . 'configurations.json';

        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents(ROOT_DIR . 'configurations.json');
        $data = json_decode($json, true);

        return $data;
    }

    /**
     * Saves the configuration into file.
     *
     * @param array $data
     *   THe configuration.
     */
    protected function saveConfig(array $data): void
    {
        $file = ROOT_DIR . 'configurations.json';

        $json = json_encode($data);
        file_put_contents($file, $json);
    }
}
