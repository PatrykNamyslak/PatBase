<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;


/**
 * Adds $table capture using the `Table::table()` method
 */
trait Table{
    public function table(string $table): static{
        $this->table = $table;
        return $this;
    }
}