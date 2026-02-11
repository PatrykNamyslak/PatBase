<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\Into;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;

class InsertOrUpdateQuery extends Query{
    use Parameterised, Into;

    protected string $insertQuery;


    /**
     * Set the parameters for Parameterised statements
     * @param array $columns
     */
    public function __construct(Patbase $db, array $columns){
        parent::__construct($db, $columns);
        $this->setParameters($this->columns);
    }


    protected function beforeBuild(): void{
        // Get the `insert` part of the query from the already existing `InsertQuery::build()` logic and remove the end semi colon from the query
        $this->insertQuery = trim(
            string: new InsertQuery(db: $this->db, columns: $this->columns)->into(table: $this->table)->build()->builtQuery(), 
            characters: ";"
            );
    }

    /**
     * Builds the query
     * @return void
     */
    protected function buildLogic(): void{
        $this->query = $this->insertQuery . " " . "ON DUPLICATE KEY UPDATE";
        $lastParameter = $this->parameters[count($this->parameters) - 1];
        // Add the update logic to the query
        foreach($this->parameters as $parameter){
            $column = trim($parameter, ":");
            $this->query .= " " . $column . " = VALUES($column)";
            // Append a comma if not at last parameter.
            if ($parameter !== $lastParameter){
                $this->query .= ",";
            }
        }
        $this->query .= ";";
    }


}