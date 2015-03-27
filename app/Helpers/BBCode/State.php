<?php

namespace App\Helpers\BBCode;

class State
{
    public $text;
    public $length;
    public $index;

    function __construct($text)
    {
        $this->text = $text;
        $this->length = strlen($text);
        $this->index = 0;
    }

    function Done()
    {
        return $this->index >= $this->length;
    }

    function ScanTo($char)
    {
        $pos = strpos($this->text, $char, $this->index);
        if ($pos === false) $pos = strlen($this->text);
        $ret = substr($this->text, $this->index, $pos - $this->index);
        $this->index = $pos;
        return $ret;
    }

    function PeekTo($str)
    {
        $pos = strpos($this->text, $str, $this->index);
        if ($pos === false) return false;
        return substr($this->text, $this->index, $pos - $this->index);
    }

    function Index()
    {
        return $this->index;
    }

    function Seek($index, $fromStart)
    {
        $this->index = $fromStart ? $index : $this->index + $index;
    }

    function Peek($count)
    {
        return substr($this->text, $this->index, $count);
    }

    function Next()
    {
        if ($this->Done()) return '';
        $this->index++;
        return $this->text[$this->index - 1];
    }

    function GetToken()
    {
        if ($this->Done() || $this->text[$this->index] != '[') return false;
        $found = false;
        $tok = '';
        for ($i = $this->index + 1; $i < min($this->index + 10, $this->length); $i++) {
            $char = $this->text[$i];
            if ($char == ' ' || $char == '=' || $char == ']') {
                $found = strlen($tok) > 0;
                break;
            }
            $tok .= $char;
        }
        return $found ? $tok : false;
    }
}