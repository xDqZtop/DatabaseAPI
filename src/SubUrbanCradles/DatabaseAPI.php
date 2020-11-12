<?php

declare(strict_types=1);

namespace SubUrbanCradles;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as E;

class DatabaseAPI extends PluginBase
{

    private static $this;

    /**
     * @param $path
     * @param $name
     * @param $data
     */
    public static function getData($path, $name, $data)
    {
        self::Database($path, $name)->get($data);
    }

    /**
     * @param $path
     * @param $name
     * @return Config
     */
    public static function Database($path, $name)
    {
        return new Config ($path . $name . '.yml');
    }

    /**
     * @param $path
     * @param $name
     * @param $data
     * @param $value
     * @param $save
     */
    public static function addData($path, $name, $data, $value, $save)
    {
        $Database = self::Database($path, $name);
        if (is_array($Total = $Database->get($data))) {
            $Total[] = $value;
            self::setData($path, $name, $data, $Total, $save);
        } else {
            self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($data) is not array in Database ($name)");
        }
    }

    /**
     * @param $path
     * @param $name
     * @param $data
     * @param $value
     * @param $save
     */
    public static function setData($path, $name, $data, $value, $save)
    {
        $Database = self::Database($path, $name);
        $Database->set($data, $value);
        if ($save == true) {
            $Database->save();
        }
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$this;
    }

    /**
     * @param $path
     * @param $name
     * @param $data
     * @param $value
     * @param $save
     */
    public static function removeData($path, $name, $data, $value, $save)
    {
        $Database = self::Database($path, $name);
        if (is_array($Total = $Database->get($data))) {
            if (in_array($value, $Total)) {
                unset($Total[array_search($value, $Total)]);
                $Total = array_values($Total);
                self::setData($path, $name, $data, $Total, $save);
            } else {
                self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($value) is not exists in array ($data) in Database ($name)");
            }
        } else {
            self::getInstance()->getLogger()->error(E::GOLD . '[' . E::AQUA . __FUNCTION__ . E::GOLD . '] ' . E::DARK_RED . "($data) is not array in Database ($name)");
        }
    }

    public function onEnable()
    {
        self::$this = $this;
    }
}
