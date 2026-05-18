<?php

namespace WishgranterProject\Backend\Service;

/**
 * Service to centralize application settings.
 */
class Settings
{
    /**
     * Constructor.
     *
     * @param array $settings
     *   Associative array with our settings.
     */
    public function __construct(protected array $settings = [])
    {
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Retrieve a specific setting.
     *
     * @param string $setting
     *   The setting name.
     * @param mixed $default
     *   The default value if the setting is not set.
     */
    public function get(string $setting, mixed $default = null): mixed
    {
        return isset($this->settings[$setting])
            ? $this->settings[$setting]
            : $default;
    }
}
