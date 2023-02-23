<?php

namespace Silverd\OhMyHadoop\Services\DBQuery\Drivers;

abstract class AbstractDriver
{
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    abstract public function validate();

    abstract public function connection(string $db = '');

    abstract public function createDatabase(string $db);

    abstract public function query(string $db, string $sql);

    abstract public function select(string $db, string $sql);

    abstract public function databases();

    abstract public function tables(string $db);

    abstract public function fields(array $reader);

    abstract public function capacity(array $reader);

    public function partitions(array $reader)
    {
        return [];
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function allFields(array $reader)
    {
        $fields = $this->fields($reader);

        return [$fields, []];
    }
}
