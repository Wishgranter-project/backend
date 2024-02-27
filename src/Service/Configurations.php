<?php

namespace WishgranterProject\Backend\Service;

class Configurations extends Singleton
{
    public function get(string $key, $default = null)
    {
        $config = $this->loadConfig();

        return isset($config[$key])
            ? $config[$key]
            : $default;
    }

    public function set(string $key, $value)
    {
        $config = $this->loadConfig();

        $config[$key] = $value;

        $this->saveConfig($config);
    }

    protected function loadConfig()
    {
        $file = ROOT_DIR . 'configurations.json';

        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents(ROOT_DIR . 'configurations.json');
        $data = json_decode($json, true);

        return $data;
    }

    protected function saveConfig(array $data)
    {
        $file = ROOT_DIR . 'configurations.json';

        $json = json_encode($data);
        file_put_contents($file, $json);
    }
}
