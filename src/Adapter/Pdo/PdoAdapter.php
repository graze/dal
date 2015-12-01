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
     * @param string $configPath
     *
     * @return static
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public static function factory(ExtendedPdo $db, $configPath)
    {
        $parser = new Parser();
        $config = $parser->parse(file_get_contents($configPath));
        return new static($db, new Configuration($db, $config));
    }
}
