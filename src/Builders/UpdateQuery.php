<?php
namespace PatrykNamyslak\Patbase\Builders;

use PatrykNamyslak\Patbase\Blueprints\Query;
use PatrykNamyslak\Patbase\Traits\Builder\Parameterised;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;

class UpdateQuery extends Query{
    use Parameterised, WhereClause;
    protected function buildLogic(): void{}
}