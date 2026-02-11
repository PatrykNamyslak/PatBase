<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;


/**
 * Adds $table capture using the `From::from()` method
 */
trait From{
    public function from(string $table){
        $this->table = $table;
        return $this;
    }
}