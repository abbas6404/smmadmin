<?php

namespace App\Support;

use App\Models\Setting as SettingModel;

class Setting
{
    protected $settings = [];
    protected $loaded = false;

    public function all()
    {
        $this->load();
        return $this->settings;
    }

    public function get($key, $default = null)
    {
        $this->load();
        return $this->settings[$key] ?? $default;
    }

    public function set($key, $value = null)
    {
        $this->load();
        
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->settings[$k] = $v;
            }
        } else {
            $this->settings[$key] = $value;
        }

        return $this;
    }

    public function save()
    {
        foreach ($this->settings as $key => $value) {
            SettingModel::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    protected function load()
    {
        if (!$this->loaded) {
            $settings = SettingModel::all();
            foreach ($settings as $setting) {
                $this->settings[$setting->key] = $setting->value;
            }
            $this->loaded = true;
        }
    }
} 