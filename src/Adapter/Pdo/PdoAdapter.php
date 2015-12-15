<?php

namespace Graze\Dal\Adapter\Pdo;

use Aura\Sql\ExtendedPdo;
use Graze\Dal\Adapter\AbstractAdapter;
use Graze\Dal\Adapter\AdapterInterface;
use Graze\Dal\Adapter\Pdo\Configuration\Configuration;
use Graze\Dal\Configuration\ConfigurationInterface;
use Graze\Dal\Relationship\ManyToManyInterface;
use Symfony\Component\Yaml\Parser;

class PdoAdapter extends AbstractAdapter implements AdapterInterface, ManyToManyInterface
{
    /**
     * @var ExtendedPdo
     */
    private $db;

    /**
     * @param ExtendedPdo $db
     * @param ConfigurationInterface $config
     */
    public function __construct(ExtendedPdo $db, ConfigurationInterface $config)
    {
        parent::__construct($config);
        $this->db = $db;
    }

    /**
     * @param string $table
     * @param array $data
     */
    public function insert($table, array $data)
    {
        $cols = implode(',', array_keys($data));
        $vals = array_values($data);

        $stmt = "INSERT INTO `{$table}` ({$cols}) VALUES (?)";
        $this->db->perform($stmt, [1 => $vals]);
    }

    /**
     * @param string $sql
     * @param array $bindings
     *
     * @return array
     */
    public function fetchCol($sql, array $bindings)
    {
        return $this->db->fetchCol($sql, $bindings);
    }

    /**
     * @param ExtendedPdo $db
     * @param array $yamlPaths
     * @param string $cacheFile
     *
     * @return PdoAdapter
     */
    public static function createFromYaml(ExtendedPdo $db, array $yamlPaths, $cacheFile = null)
    {
        if ($cacheFile && file_exists($cacheFile)) {
            return static::createFromCache($db, $cacheFile);
        }

        $config = [];
        $parser = new Parser();

        foreach ($yamlPaths as $yamlPath) {
            $config = array_merge($config, $parser->parse(file_get_contents($yamlPath)));
        }

        if ($cacheFile) {
            file_put_contents($cacheFile, json_encode($config));
        }

        return static::createFromArray($db, $config);
    }

    /**
     * @param ExtendedPdo $db
     * @param array $config
     *
     * @return PdoAdapter
     */
    public static function createFromArray(ExtendedPdo $db, array $config)
    {
        return new static($db, new Configuration($db, $config));
    }

    /**
     * @param ExtendedPdo $db
     * @param string $cacheFile
     *
     * @return PdoAdapter
     */
    private static function createFromCache(ExtendedPdo $db, $cacheFile)
    {
        $config = json_decode(file_get_contents($cacheFile), true);
        return static::createFromArray($db, $config);
    }
}
