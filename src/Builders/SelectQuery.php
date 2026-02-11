<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\From;
use PatrykNamyslak\Patbase\Traits\Builder\Limit;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;

class SelectQuery extends Query{
    use From, WhereClause, Limit, Parameterised;

    public function buildLogic(): void{
        $this->query = "SELECT " . $this->columnsToString() . " FROM `{$this->table}`";
    }

    /**
     * An alias for `SelectQuery::all()`
     * @return mixed
     */
    public function run(): mixed{
        return $this->all();
    }

    public function all(): mixed{
        $this->build();
        return $this->db->prepare($this->query, $this->preparedValues)->fetchAll();
    }

    /**
     * Return the first result
     * @return static
     */
    public function first(): mixed{
        $this->build();
        return $this->db->prepare($this->query, $this->preparedValues)->fetch();
    }
}