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
    public function __construct(Patbase $db, array $columns){
        parent::__construct(db: $db, columns: $columns);
        $this->setParameters(columns: $this->columns);
    }

    /**
     * Hook to work with an array version of the `columns` before they are parsed into a string
     * @return void
     */
    protected function beforeBuild(): void{
        $this->setParameters(columns: $this->columns);
    }

    /**
     * Builds the query
     * @return void
     */
    protected function buildLogic(): void{
        $this->query = "INSERT INTO `{$this->table}` (" . $this->columnsToString() . ") VALUES({$this->parametersToString()});";
    }

}