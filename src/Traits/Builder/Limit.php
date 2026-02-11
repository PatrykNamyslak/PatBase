<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;

use PatrykNamyslak\Patbase\Facades\DB;


trait Limit{
    /**
     * Limit the amount of results returned by the query
     * @var 
     */
    protected ?int $limit = NULL;

    protected function Limit__afterBuild(){
        if ($this->limit !== NULL){
            $this->query .= " LIMIT {$this->limit}";
        }
    }

    /**
     * Limit the amount of results
     * @param int $numberOfRows
     */
    public function limit(int $numberOfRows){
        $this->limit = $numberOfRows;
        return $this;
    }
}