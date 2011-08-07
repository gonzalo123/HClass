<?php
namespace HClass;

class Fct
{
    static function create($name, \Closure $fct)
    {
        return new self($name, $fct);
    }

    protected $_name;
    function __construct($name, \Closure $fct)
    {
        $this->_name = $name;
        $this->_fct = $fct;
    }

    function getName()
    {
        return $this->_name;
    }

    protected $_fct;
    function getCallBack()
    {
        return $this->_fct;
    }
}
