<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\From;
use PatrykNamyslak\Patbase\Traits\Builder\Limit;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;

class SelectQuery extends Query{
    use From, WhereClause, Limit, Parameterised;

    protected function buildLogic(): void{
        $this->query = "SELECT " . $this->columnsToString() . " FROM `{$this->table}`";
    }


    /**
     * Fetch all of the results
     * @return mixed
     */
    public function all(): mixed{
        $this->build();
        return $this->run(fetch: true);
    }

    /**
     * Return the first result
     * @return static
     */
    public function first(): mixed{
        $this->build();
        return $this->run(fetch: true, singular: true);
    }
}