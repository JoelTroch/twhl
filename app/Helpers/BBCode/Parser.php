<?php

namespace App\Helpers\BBCode;

use App\Helpers\BBCode\Elements\DefaultElement;

/**
 *
 * Markdown lists * / #
 * Markdown headings ===
 * Markdown code (whitespace)
 * Markdown-ish tables |- / |=
 * htmlspecialchars
 * quotes
 * newlines
 * autolinking
 *
 * Wiki contents
 * Wiki categories [cat:Test]
 * [[Wiki links]] / [www.example.com|Example]
 * Wiki images [img:example.png]
 *
 * Embedded image
 * Embedded youtube
 *
 * Smilies
 *
 * embedded : [youtube:oGlhgVz5r6E|small|Caption]
 * inline
 * cleanup
 *
 * Class Parser
 * @package App\Helpers\BBCode
 */
class Parser
{
    public $elements = array();
    public $tags = array();
    public $processors = array();

    function __construct($config = array())
    {
        $this->LoadConfiguration($config);
    }

    function LoadConfiguration($config) {
        if (array_key_exists('elements', $config) && is_array($config['elements'])) {
            foreach ($config['elements'] as $cfg) {
                if (!array_key_exists('class', $cfg)) continue;
                $cls = $cfg['class'];
                $e = new $cls($cfg);
                foreach ($cfg as $k => $v) {
                    if ($k == 'class') continue;
                    $e->$k = $v;
                }
                $this->elements[] = $e;
            }
        }
        if (array_key_exists('tags', $config) && is_array($config['elements'])) {
            foreach ($config['tags'] as $cfg) {
                if (!array_key_exists('class', $cfg)) continue;
                $cls = $cfg['class'];
                $e = new $cls($cfg);
                foreach ($cfg as $k => $v) {
                    if ($k == 'class') continue;
                    $e->$k = $v;
                }
                $this->tags[] = $e;
            }
        }
        if (array_key_exists('processors', $config) && is_array($config['elements'])) {
            foreach ($config['processors'] as $cfg) {
                if (!array_key_exists('class', $cfg)) continue;
                $cls = $cfg['class'];
                $e = new $cls($cfg);
                foreach ($cfg as $k => $v) {
                    if ($k == 'class') continue;
                    $e->$k = $v;
                }
                $this->processors[] = $e;
            }
        }
    }

    public function Parse($text, $scope = '') {
        $result = $this->ParseResult($text, $scope);
        return $result->text;
    }

    public function ParseResult($text, $scope = '') {
        $text = str_replace("\r", "", $text);
        $elements = $this->SplitElements($text, $scope);
        $result = new ParseResult();
        $str = '';
        foreach ($elements as $e) {
            $str .= $e->Parse($result, $scope) . "\n";
        }
        $result->text = trim($str);
        return $result;
    }

    public function SplitElements($text, $scope) {
        $lines = new Lines($text);
        $elements = array();
        $default = array();
        $inscope = array();

        // Only use the elements in scope
        foreach ($this->elements as $e) {
            if ($e->InScope($scope)) $inscope[] = $e;
        }

        while ($lines->Next())
        {
            $matched = false;
            foreach ($inscope as $e)
            {
                if ($e->Matches($lines))
                {
                    // We've got a new element match - add the default element first
                    if (count($default) > 0) $elements[] = new DefaultElement($this, $default);
                    $default = array();

                    // Add the new element
                    $elements[] = $e->Consume($this, $lines);
                    $matched = true;

                    break;
                }
            }
            if (!$matched) $default[] = $lines->Value();
        }
        if (count($default) > 0) $elements[] = new DefaultElement($this, $default);
        return $elements;
    }

    public function CleanString($text) {
        $text = htmlspecialchars($text);
        return $text;
    }

    public function PostProcessString($result, $text, $scope) {
        $str = $text;
        foreach ($this->processors as $pp) {
            if ($pp->InScope($scope)) {
                $str = $pp->Process($result, $str, $scope);
            }
        }
        return $str;
    }

    public function ParseBBCode($result, $text, $scope, $type) {
        $text = $this->CleanString($text);
        $state = new State($text);
        $str = '';

        $inscope = array();

        // Only use the tags in scope
        foreach ($this->tags as $t) {
            if ($t->InScope($scope, $type)) $inscope[] = $t;
        }

        while (!$state->Done())
        {
            $str .= $state->ScanTo('[');
            $token = $state->GetToken();
            $found = false;
            foreach ($inscope as $t) {
                if ($t->Matches($state, $token)) {
                    $parsed = $t->Parse($result, $this, $state, $scope);
                    if ($parsed) {
                        $str .= $parsed;
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) $str .= $state->Next();
        }

        $str = $this->PostProcessString($result, $str, $scope);
        return $str;
    }
}