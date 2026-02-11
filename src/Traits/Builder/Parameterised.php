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
    protected array $parameters;


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
        $this->parameters = array_map(function($column){
            return $column = ":" . $column;
        }, $columns);
        return $this;
    }


    protected function parametersToString(){
        return implode(",", $this->parameters);
    }

    /**
     * Set the value of a parameterised value
     * @param string $parameter
     * @param string $value
     * @throws UnexpectedValueException When $parameter is invalid
     * @return static
     */
    public function set(string $parameter, string $value): static{
        // Make sure that the format is as expected -> :paramName
        if (str_split($parameter)[0] !== ":"){
            $parameter = ":" . $parameter;
        }
        if (!in_array($parameter, $this->parameters)){
            throw new UnexpectedValueException('$parameter does not exist in ' . self::class . '::$parameters');
        }
        $this->preparedValues[$parameter] = $value;
        return $this;
    }

    public function run(): bool{
        return $this->Parameterised__run();
    }

    /**
     * A parameterised run method with error catching
     * @return bool
     */
    protected function Parameterised__run(): bool{
        $this->build();
        try{
            $result = $this->db->prepare($this->query, $this->preparedValues)->execute();
        }catch(Throwable $t){
            echo "Query Failed";
            exit;
            // LOG the error
            // dev only
            echo $t->getMessage();
        }
        return $result;
    }
}