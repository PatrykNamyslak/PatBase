<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use PatrykNamyslak\Patbase\Traits\Builder\Table;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;
use PatrykNamyslak\Patbase\Traits\Builder\Core;

class UpdateQuery extends Query{
    use Parameterised, WhereClause, Table;

    protected function buildLogic(): void{
        $this->query = "UPDATE `{$this->table}` SET ";
        $lastSetParameter = $this->array_last($this->getSetParameters());
        foreach($this->getSetParameters() as $parameterName){
            // If the parameter has the expected prefix `:set`
            if (str_contains($parameterName, ":set")){
                $this->query .= "`{$this->getColumnFromParameter($parameterName)}` = {$parameterName}";
                if ($parameterName !== $lastSetParameter){
                    $this->query .= ", ";
                }else{
                    continue;
                }
            }
            continue;
        }
    }
}