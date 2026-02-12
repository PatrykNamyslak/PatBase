<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;

use PatrykNamyslak\Patbase;
use Throwable;
use UnexpectedValueException;

trait Parameterised{

    /**
     * Holds the parameters such as `:email`, Useful for any prepared statement.
     * @var string[]
     */
    protected array $parameters = [];


    /**
     * An `associative` array that uses `Parameterised::$parameters` as keys and its assigned value by the `Parameterised::set()` method
     * @var string[]
     */
    protected(set) array $preparedValues = [];

    /**
     * Set the parameters on instantiation
     * @param array $columns
     */
    public function __construct(Patbase $db, ?array $columns = NULL){
        parent::__construct($db, $columns);
        if ($columns){
            $this->setParameters($columns);
        }else{
            $this->setParameters($this->getParametersFromClauses());
        }
    }


    /**
     * Sets the parameters by prepending a semicolon to make it clear its a parameter name
     * @param array $columns
     * @return void
     */
    protected function setParameters(array $columns): static{
        if (count($columns) === 1 and $columns[0] === "*"){
            $this->parameters[] = $columns[0];
            return $this;
        }
        $this->parameters = array_map(function($column){
            return $column = ":" . $column;
        }, $columns);
        return $this;
    }


    /**
     * Utility function to set parameters from a Parameters array.
     * @param array $parameters
     */
    public function setParametersFromParamsArray(array $parameters): static{
        $this->parameters = $parameters;
        return $this;
    }

    protected function parametersToString(){
        return implode(",", $this->getSetParameters());
    }

    /**
     * Set the value of a parameterised value
     * @param string $parameter The name of the column
     * @param string $value
     * @throws UnexpectedValueException When $parameter is invalid
     * * Make sure that the format is as expected -> `:paramName`
     * @return static
     */
    public function set(string $parameter, string $value): static{
        if (str_split($parameter)[0] !== ":"){
            $parameter = ":" . $parameter;
        }
        if (!in_array($parameter, $this->parameters)){
            throw new UnexpectedValueException('$parameter does not exist in ' . self::class . '::$parameters');
        }
        // Remove the colon to append the set prefix to not mix up with the where clause params
        $this->preparedValues[":set" . $this->getColumnFromParameter($parameter)] = $value;
        return $this;
    }

    /**
     * @param bool $fetch Determines if the query is a fetch query or a regular expression
     * @param bool $singular Deteremines whether a singular record should be returned
     * * Returns `array`: when `$fetch` is `true` AND `FETCH_MODE` is set to ASSOC
     * * Returns `Object[]` when `$fetch` is `true` AND `FETCH_MODE` is set to OBJ
     * * Returns `bool` when `$fetch` is `FALSE`
     */
    public function run(bool $fetch = false, bool $singular = false): mixed{
        $this->preparedValues = match (true){
            $this->usesTrait(WhereClause::class) => array_merge($this->preparedValues, $this->whereClauseValues()),
            default => $this->preparedValues,
        };
        return $this->Parameterised__run($fetch, $singular);
    }

    /**
     * A parameterised run method with error catching
     * @param bool $fetch Determines if the query is a fetch query or a regular expression
     * @param bool $singular Deteremines whether a singular record should be returned
     * * Returns `array`: when `$fetch` is `true` AND `FETCH_MODE` is set to ASSOC
     * * Returns `Object[]` when `$fetch` is `true` AND `FETCH_MODE` is set to OBJ
     * * Returns `bool` when `$fetch` is `FALSE`
     */
    protected function Parameterised__run(bool $fetch = false, bool $singular = false): mixed{
        $this->prepareValues();
        $this->build();
        try{
            $result = match (true){
                $fetch and $singular => $this->db->prepare($this->query, $this->preparedValues)->fetch(),
                $fetch and !$singular => $this->db->prepare($this->query, $this->preparedValues)->fetchAll(),
                default => $this->db->prepare($this->query, $this->preparedValues)->execute(),
            };
        }catch(Throwable $t){
            echo $t->getMessage();
            echo "Query Failed";
            exit;
            // LOG the error
            // dev only
        }
        return $result;
    }

    /**
     * Remove the prefixed `:` or any prefix such as `:set` or `:where`
     * @param string $parameter
     * @return string
     */
    protected function getColumnFromParameter(string $parameter){
        return match(true){
            str_contains($parameter, ":set") => str_replace(":set", "", $parameter),
            str_contains($parameter, ":where") => str_replace(":where", "", $parameter),
            str_contains($parameter, ":") => str_replace(":", "", $parameter),
            // If the parameter is already a column and does not have a parameter prefix
            default => $parameter,
        };
    }


    /**
     * Get the active parameters from the `$this->preparedValues` array by getting all of the keys
     * @param string[] $extraParameters
     * @return array
     */
    public function parameters(array $extraParameters = []): array{
        $this->parameters = array_values(array_unique(
            array: array_merge($this->parameters, array_keys($this->preparedValues), $extraParameters)
            ));
        return $this->parameters;
    }


    /**
     * Returns the SET parameters that are used in the query by filtering the currently set parameters and returning the only ones with a `:set` prefix
     * @return array
     */
    protected function getSetParameters(): array{
        return array_filter(
            array: $this->parameters(), 
            callback: function ($parameter) {
                return str_contains(haystack: $parameter, needle: ":set");
            }
        );
    }

    /**
     * Returns the WHERE parameters that are used in the query by filtering the currently set parameters and returning the only ones with a `:where` prefix
     * @return array
     */
    protected function getWhereParameters(): array{
        return array_filter(
            array: $this->parameters(), 
            callback: function ($parameter): bool {
                return str_contains(haystack: $parameter, needle: ":where");
            }
        );
    }

    protected function prepareValues(){
        $this->preparedValues = match(true){
            class_uses(WhereClause::class) => array_merge($this->getWhereClausesAsArray(), $this->preparedValues),
            default => $this->preparedValues,
        };
    }
}