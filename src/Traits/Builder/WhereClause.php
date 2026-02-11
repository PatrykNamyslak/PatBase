<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;

use PatrykNamyslak\Patbase\Enums\WhereOperator;
use UnexpectedValueException;

trait WhereClause{
    /**
     * Where clauses for the query
     * @var string[]
     */
    protected array $whereClauses = [];

    public final function WhereClause__beforeBuild(): void{}
    public final function WhereClause__afterBuild(): void{
        if ($this->hasWhereClauses()){
            $whereClauses = $this->whereClauses();

            if ($whereClauses){
                $this->query .=  " WHERE " . $whereClauses;
            }
        }
    }

    public function where(string $columnName, string|WhereOperator $operator = WhereOperator::EQUALS, string|int|bool $value): static{
        if (is_string($operator) and !in_array($operator, array_column(WhereOperator::cases(), "value"))){
            throw new UnexpectedValueException('$operator must be a valid character that is in ' . WhereOperator::class);
        }
        $operator = match (true){
            $operator instanceof WhereOperator => $operator->value,
            is_string($operator) => $operator,
            default => "="
        };
        // Create a structure for prepared statements
        $clause = [
            "statement" => "{$columnName} {$operator} :{$columnName}",
            "parameter" => ":" . $columnName,
            "value" => $value,
            "column" => $columnName,
        ];
        $this->whereClauses[] = $clause;
        return $this;
    }

    /**
     * Check whether there are where clauses, Returns `true` if `WhereClauses::whereClauses` is not an `empty array` and `false otherwise`.
     * @return bool
     */
    protected function hasWhereClauses(): bool{
        return $this->whereClauses !== [];
    }
    /**
     * Returns the whereClauses in string format or false if there are none
     * @return bool|string
     */
    protected function whereClauses(): string|false{
        return match (true){
            $this->hasWhereClauses() => implode(" AND ", array_column($this->whereClauses, "statement")),
            default => false,
        };
    }

    /**
     * Extract the parameter names
     * @return array
     */
    protected function getParametersFromClauses(){
        return array_keys($this->whereClauses, "parameter");
    }


    protected function whereClauseValues(){
        $values = [];
        foreach($this->whereClauses as $clause){
            $values[$clause["parameter"]] = $clause["value"];
        }
        return $values;
    }
}