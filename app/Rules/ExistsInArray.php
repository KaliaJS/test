<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsInArray implements Rule
{
    private $table;
    private $column;
    private $missingValues = [];
    
    public function __construct($table, $column = 'id')
    {
        $this->table = $table;
        $this->column = $column;
    }
    
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }
        
        $existingValues = DB::table($this->table)
            ->whereIn($this->column, $value)
            ->pluck($this->column)
            ->toArray();
        
        $this->missingValues = array_diff($value, $existingValues);
        
        return empty($this->missingValues);
    }
    
    public function message()
    {
        return 'Some :attribute do not exist.';
    }
}
