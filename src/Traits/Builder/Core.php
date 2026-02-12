<?php
namespace PatrykNamyslak\Patbase\Traits\Builder;

trait Core{
    /**
     * `PHP 8.5s` function implemented in `PHP 8.4`
     * @param array $array
     */
    public function array_first(array $array){
        return array_values($array)[0];
    }
    /**
     * `PHP 8.5s` function implemented in `PHP 8.4`
     * @param array $array
     */
    public function array_last(array $array){
        $array =  array_values($array);
        return $array[count($array) - 1];
    }
}