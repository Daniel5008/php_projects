<?php

namespace Src\Models;

class Model {

    private $values = [];

    public function __call($method, $args)
    {

        $methodName = substr($method, 0, 3);
        $fieldName  = substr($method, 3, strlen($method));

        switch ($methodName) {
            case 'get':
                return isset($this->values[$fieldName]) ? $this->values[$fieldName] : null;
                break;
            
            case 'set':
                $this->values[$fieldName] = $args[0];
                break;
        }
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set".$key}($value);
        }
    }


    public function getValues()
    {
        return $this->values;
    }


}