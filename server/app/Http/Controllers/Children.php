<?php

namespace App\Http\Controllers;
class Children extends BaseController
{
    public $array = array();
    public function __construct($arrays = [])
    {
        $this->array = $arrays;
    }
    public function getChildren($myId, $withSelf = false): array
    {
        $newArray = [];
        foreach ($this->array as $value) {
            if (!isset($value['id'])) {
                continue;
            }
            if ((string)$value['p_id'] == (string)$myId) {
                $newArray[] = $value;
                $newArray = array_merge($newArray, $this->getChildren($value['id']));
            } elseif ($withSelf && (string)$value['id'] == (string)$myId) {
                $newArray[] = $value;
            }
        }
        return $newArray;
    }
}
