<?php
namespace PatrykNamyslak\Patbase\Blueprints;

use PatrykNamyslak\Patbase;
use PatrykNamyslak\Patbase\Traits\Builder\Core;
use PatrykNamyslak\Patbase\Traits\Builder\Limit;
use PatrykNamyslak\Patbase\Traits\Builder\WhereClause;
use ReflectionClass;

/**
 * Base Query Builder
 */
abstract class Query{
    use Core;
    
    private static array $AFTER_BUILD_ORDER;
    /**
     * DO NOT USE "use" statements, List any traits here like this, WhereClause::class and in the __beforeBuild() we make sure to include these traits
     * @var array
     */
    private(set) array $traits = [];

    /**
     * Target table to query
     * @var string
     */
    protected string $table;

    /**
     * The final query
     * @var string
     */
    public string $query;

    /**
     * Initiates the Query builder
     * @param Patbase $db Database interface to execute the query
     * @param string[] $columns The columns / Fields to use in the query in string form, usually imploded from an array
     */
    public function __construct(protected Patbase $db, protected ?array $columns = NULL){
        $this->traits = class_uses($this);
        self::$AFTER_BUILD_ORDER = [
            new ReflectionClass(WhereClause::class)->getShortName(),
            new ReflectionClass(Limit::class)->getShortName(),
        ];
    }

    abstract protected function buildLogic(): void;

    /**
     * Build the final query
     * @return static
     */
    protected final function build(): static{
        // User defined
        $this->beforeBuild();
        // Required Logic
        $this->__beforeBuild();
        $this->buildLogic();
        // Required Logic
        $this->__afterBuild();
        // User defined
        $this->afterBuild();
        return $this;
    }

    /**
     * Magic method for `REQUIRED` logic for traits such as `PatrykNamyslak\Patbase\Traits\WhereClause::class`
     * * This magic method gets the traits that are declared inside of the constructor and assign it to $this->traits and now we can go through each one and run $traitName__beforeBuild() on each one
     */
    protected final function __beforeBuild(): void{
        foreach ($this->traits as $trait){
            $trait = new ReflectionClass($trait)->getShortName();
            $trait__beforeBuild = "{$trait}__beforeBuild";
            // Not every trait will have a magic method
            if (method_exists($this, $trait__beforeBuild)){
                $this->$trait__beforeBuild();
            }
        }
    }

    /**
     * Runs all of the prefixed __afterBuild() magic functions that are prefixed by the trait itself, they are run in the order of self::$AFTER_BUILD_ORDER
     * 
     * Magic method for `REQUIRED` logic for traits such as `PatrykNamyslak\Patbase\Traits\WhereClause::class`
     * * This magic method gets the traits that are declared inside of the constructor and assign it to $this->traits and now we can go through each one and run $traitName_afterBuild() on each one
     * * There is a queue system now based on the self::$AFTER_BUILD_ORDER static property;
     */
    protected final function __afterBuild(): void{
        $queue = [];
        /** This will be `appended` to the `END` of the ordered `$queue` */
        $queueEnd = [];
        foreach ($this->traits as $trait){
            $trait = new ReflectionClass($trait)->getShortName();
            $trait__afterBuild = "{$trait}__afterBuild";
            // Not every trait will have a magic method
            if (method_exists($this, $trait__afterBuild)){
                $positionInQueue = array_search($trait, self::$AFTER_BUILD_ORDER);
                // if its in the build order
                if ($positionInQueue !== false){
                    $queue[$positionInQueue] = $trait;
                }else{
                    $queueEnd[] = $trait;
                }
            }
        }
        // Sort the queue by its position assigned in self::$AFTER_BUILD_ORDER
        ksort($queue);
        // Append the rest of the traits into the queue that did not require to be in any specific order.
        if ($queueEnd !== []){
            $queue = array_merge($queue, $queueEnd);
        }
        foreach($queue as $trait){
            $trait__afterBuild = "{$trait}__afterBuild";
            $this->$trait__afterBuild();
        }
        $this->query .= ";";
    }



    /**
     * A Hook to run some logic `BEFORE` building the query
     * @return void
     */
    protected function beforeBuild(){}
    /**
     * A Hook to run some logic `AFTER` building the query
     * @return void
     */
    protected function afterBuild(){}

    /**
     * Takes a reference of a columns array and parses it into the expected type by the Query Builder
     * @param array $columns
     * @return string
     */
    protected final function columnsToString(): string{
        return implode(",", $this->columns);
    }

    /**
     * Returns the final built query
     * @return string
     */
    public final function builtQuery(){
        $this->build();
        return $this->query;
    }

    abstract function run();

    /**
     * Check if the current class uses a `$trait`
     * @param string $trait Use Trait::class
     * @return bool
     */
    protected function usesTrait(string $trait): bool{
        return in_array($trait, $this->traits);
    }
}