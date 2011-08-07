<?php
namespace HClass;

class HCls
{
    private $_conf;
    
    function __construct(HClass $conf)
    {
        $this->_conf = $conf;
    }

    public function __call($methodName, $args=array())
    {
        if (array_key_exists($methodName, $this->_conf->fcs)) {
            $method = $this->_conf->fcs[$methodName];
            return call_user_func_array($method->getCallBack(), array_merge(array('self' => $this), $args));
        } else {
            throw new \Exception("ERROR Method '{$methodName}' does not exits");
        }
    }
}
