<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\From;
use PatrykNamyslak\Patbase\Traits\Builder\Limit;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;

class DeleteQuery extends Query{
    use From, WhereClause, Limit, Parameterised;

    public function __construct(Patbase $db){
        parent::__construct($db);
    }


    protected function buildLogic(): void{
        $this->query = "DELETE FROM `{$this->table}`";
    }
}