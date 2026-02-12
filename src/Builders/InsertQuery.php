<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\Into;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use Throwable;

class InsertQuery extends Query{
    use Parameterised, Into;

    /**
     * Set the parameters for Parameterised statements
     * @param array $columns
     */
    public function __construct(Patbase $db, array $columns, bool $upsert = false){
        parent::__construct(db: $db, columns: $columns);

        // Set the parameters if not a part of an upsert statement / query as an upsert statement injects the parameters
        if (!$upsert){
            $this->setParameters(columns: $this->columns);
        }
    }

    /**
     * Builds the query
     * @return void
     */
    protected function buildLogic(): void{
        $this->query = "INSERT INTO `{$this->table}` (" . $this->columnsToString() . ") VALUES({$this->parametersToString()});";
    }

}