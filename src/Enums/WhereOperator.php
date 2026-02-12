<?php
namespace PatrykNamyslak\Patbase\Enums;

enum WhereOperator:string{
    case EQUALS = "=";
    case LESS_THAN = "<";
    case GREATER_THAN = ">";
    case CONTAINS = "LIKE";
}