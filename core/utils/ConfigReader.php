<?php

namespace core\utils;

use Exception;
use Symfony\Component\Yaml\Yaml;

class ConfigReader
{

  private $configFileName;
  private $config;

  public function __construct($configName)
  {
    $this->configFileName = $configName;
  }

  /**
   * @return mixed
   * @throws Exception
   */
  private function getConfig()
  {

    if (empty($this->config)) {

      $filePath = __DIR__ . '/../../' . $this->configFileName . '.config.yml';
      if (!file_exists($filePath)) {
        throw new Exception('Config \'' . $this->configFileName . '\' could not be found.');
      }
      $this->config = Yaml::parse(file_get_contents($filePath));
    }
    return $this->config;

  }

  /**
   * @param $key
   * @return string|array
   * @throws Exception
   */
  public function requireConfig($key)
  {

    $keys = $key;
    if (!is_array($key)) {
      $keys = [$key];
    }

    $config = $this->getConfig();

    $keyValues = [];
    $missing = [];
    foreach ($keys as $key) {
      if (!array_key_exists($key, $config) || $config[$key] == '') {
        $missing[] = '\'' . $key . '\'';
      } else {
        $keyValues[$key] = $config[$key];
      }
    }

    if (!empty($missing)) {
      throw new Exception('Config file does not have ' . implode(', ', $missing) . ' defined.');
    } else {
      if (!is_array($key)) {
        return $config[$key];
      } else {
        return $keyValues;
      }
    }

  }

}