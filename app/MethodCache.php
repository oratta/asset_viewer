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
            if($this->relationLoaded($key)) {
                return true;
            }
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
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
            if($this->relationLoaded($key)) {
                return $this->relations[$key];
            }
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
            }
        }

        return $this->__methodCache[$key];
    }

    public function setCache($key=null, $value)
    {
        if(!$key){
            $key = $this->getCallFrom();
        }
        else {
            if($this->relationLoaded($key)) {
                $this->relations[$key] = $value;
                return;
            }
            if($this->hasGetMutator($key)){
                $key = 'get'.Str::studly($key).'Attribute';
            }
        }

        $this->__methodCache[$key] = $value;
    }

    private  function getCallFrom()
    {
        $bt = debug_backtrace();
        return $bt[1]['function'];
    }
}