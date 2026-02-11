<?php
namespace PatrykNamyslak\Patbase\Enums;


enum QueryType: string{

    case SELECT = "SELECT";
    case INSERT_OR_UPDATE = "UNIMPORTANT_VAL";
    case INSERT = "SOME_OTHER_UNIMPORTANT_VAL";
    case UPDATE = "UPDATE";
    case DELETE = "DELETE";

    public function getKeyword(){
        return match($this){
            QueryType::INSERT_OR_UPDATE, QueryType::INSERT => "INSERT",
            default => $this->value,
        };
    }
}