<?php

declare(strict_types=1);

namespace SubUrbanCradles;

use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as E;

class DatabaseAPI extends PluginBase
{
    private static ?self $instance = null;

    /**
     * @param string $path
     * @param string $name
     * @param string $data
     * @return mixed
     */
    public static function getData(string $path, string $name, string $data): mixed
    {
        return self::Database($path, $name)->get($data);
    }

    /**
     * @param string $path
     * @param string $name
     * @return Config
     */
    public static function Database(string $path, string $name): Config
    {
        return new Config($path . $name . '.yml', Config::YAML);
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $data
     * @param mixed $value
     * @param bool $save
     * @throws JsonException
     */
    public static function addData(string $path, string $name, string $data, mixed $value, bool $save): void
    {
        $Database = self::Database($path, $name);
        $Total = $Database->get($data);
        if (is_array($Total)) {
            $Total[] = $value;
            self::setData($path, $name, $data, $Total, $save);
        } else {
            self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($data) is not array in Database ($name)");
        }
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $data
     * @param mixed $value
     * @param bool $save
     * @throws JsonException
     */
    public static function setData(string $path, string $name, string $data, mixed $value, bool $save): void
    {
        $Database = self::Database($path, $name);
        $Database->set($data, $value);
        if ($save) {
            $Database->save();
        }
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $data
     * @param mixed $value
     * @param bool $save
     * @throws JsonException
     */
    public static function removeData(string $path, string $name, string $data, mixed $value, bool $save): void
    {
        $Database = self::Database($path, $name);
        $Total = $Database->get($data);
        if (is_array($Total)) {
            if (in_array($value, $Total, true)) {
                unset($Total[array_search($value, $Total, true)]);
                $Total = array_values($Total);
                self::setData($path, $name, $data, $Total, $save);
            } else {
                self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($value) is not exists in array ($data) in Database ($name)");
            }
        } else {
            self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($data) is not array in Database ($name)");
        }
    }

    protected function onEnable(): void
    {
        self::$instance = $this;
    }
}
