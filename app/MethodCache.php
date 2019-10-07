<?php

namespace App;

use Illuminate\Support\Str;

trait MethodCache
{
    protected $__methodCache = [];

    public function isCached($key=null)
    {
        if(!$key){
            $key = $this->getCallFrom();
        }
        else {
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
            }
            elseif($this->relationLoaded($key)) {
                return true;
            }
        }

        return isset($this->__methodCache[$key]);
    }
    public function getCache($key=null)
    {
        if(!$key){
            $key = $this->getCallFrom();
        }
        else {
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
            }
            elseif($this->relationLoaded($key)) {
                return $this->relations[$key];
            }
        }

        return $this->__methodCache[$key];
    }

    public function setCache($value, $key=null)
    {
        if(!$key){
            $key = $this->getCallFrom();
        }
        else {
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
            }
            elseif($this->relationLoaded($key)) {
                $this->relations[$key] = $value;
                return;
            }

        }

        $this->__methodCache[$key] = $value;
    }

    private  function getCallFrom()
    {
        $bt = debug_backtrace();
        return $bt[2]['function'];
    }
}