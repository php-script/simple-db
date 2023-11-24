<?php

namespace VulcanPhp\SimpleDb\Model;

use VulcanPhp\SimpleDb\Model\Loader;
use VulcanPhp\SimpleDb\Model\Validator;

class BaseModel
{
    use Loader, Validator;

    protected array $labels = [], $properties = [];

    public const
        RULE_REQUIRED = 'required',
        RULE_EMAIL    = 'email',
        RULE_MIN      = 'min',
        RULE_MAX      = 'max',
        RULE_MATCH    = 'match',
        RULE_UNIQUE   = 'unique';

    public function labels(): array
    {
        return (array) $this->labels ?? [];
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }

    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? ucwords(str_replace(['_', '-'], ' ', $attribute));
    }

    public function getValue(string $attribute)
    {
        return $this->{$attribute} ?? '';
    }

    public function __set($name, $value = null)
    {
        $this->properties[$name] = $value;
    }

    public function __get($name)
    {
        return $this->properties[$name] ?? null;
    }

    public function __isset($name): bool
    {
        return array_key_exists($name, $this->properties) === true;
    }

    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    public function toArray(): array
    {
        return $this->properties;
    }
}
