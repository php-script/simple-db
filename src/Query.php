<?php

namespace PhpScript\SimpleDb;

use PDO;
use PhpScript\SimpleDb\Database;
use PhpScript\SimpleDb\Exceptions\QueryBuilderException;
use PhpScript\SimpleDb\Includes\HookHandler;
use PhpScript\SimpleDb\Query\QBCreate;
use PhpScript\SimpleDb\Query\QBDelete;
use PhpScript\SimpleDb\Query\QBRead;
use PhpScript\SimpleDb\Query\QBUpdate;
use PhpScript\SimpleDb\Query\QBWhere;

class Query
{
    use QBCreate, QBWhere, QBRead, QBUpdate, QBDelete;

    protected string $table, $primary;
    protected Database $database;

    public function __construct(string $table, string $primary = 'id')
    {
        $this->table = $table;
        $this->primary = $primary;
    }

    public static function table(...$args): Query
    {
        return new Query(...$args);
    }

    public function setDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }

    public function getDatabase(): Database
    {
        return $this->database ?? Database::$instance;
    }

    public function getHookHandler(): HookHandler
    {
        return $this->getDatabase()
            ->getHookHandler();
    }

    public function pdo(): PDO
    {
        return $this->getDatabase()
            ->getPdo();
    }

    public function prepare(string $query)
    {
        return $this->getDatabase()
            ->prepare($query);
    }

    protected function changed($action): void
    {
        $this->getHookHandler()
            ->doAction('changed', $this->table, $this->primary, $action);
    }

    public function __call($name, $arguments)
    {
        if (
            $this->getHookHandler()
            ->hasFallback('query', $name)
        ) {
            return $this->getHookHandler()
                ->getFallback('query', $name, $this, ...$arguments);
        }

        throw new QueryBuilderException('Undefined Method: ' . $name);
    }
}
