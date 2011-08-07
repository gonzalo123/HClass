<?php
namespace HClass;

include_once('Fct.php');
include_once('HCls.php');

use HClass\Fct;

class HClass
{
    const __construct = '__construct';
    static function define(HClass $extends=null)
    {
        return is_null($extends) ? new self($extends) : clone $extends;
    }

    private $_extends;
    function __construct(HClass $extends=null)
    {
        $this->_extends = $extends;
    }

    public $constructor;
    public $fcs = array();
    public $fields = array();

    function fct($name, \Closure $fct)
    {
        return $this->add(Fct::create($name, $fct));
    }

    function add($item)
    {
        if ($item instanceof Fct) {
            $this->fcs[$item->getName()] = $item;
        } else {
            throw new \Exception("undefined element");
        }
        return $this;
    }

    function create()
    {
        $obj = new HCls($this);

        if (isset($this->fcs[self::__construct])) {
            call_user_func_array($this->fcs[self::__construct]->getCallBack(), array_merge(array('self' => $obj), func_get_args()));
        }
        return $obj;
    }
}
