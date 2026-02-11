<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;


/**
 * Adds $table capture using the `$this->into()` method, use this for Update and insert statements for better readability
 */
trait Into{
    use From;

    /**
     * Alias for Query::from().
     * * Use this for `UPDATE` and `INSERT` statements to make it more readable. Read more at https://patl.ink/docs/patbase/query-builder/#readability
     * @param string $table
     * @return static
     */
    public function into(string $table): static{
        return $this->from($table);
    }
}