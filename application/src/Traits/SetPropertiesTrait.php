<?php
declare(strict_types=1);

namespace App\Traits;

trait SetPropertiesTrait
{
    /**
     * @param  array  $properties
     *
     * @return $this
     *
     * @noinspection PhpVariableVariableInspection
     */
    public function setProperties(array $properties): self
    {
        foreach ($properties as $property => $value) {
            if (isset($this->$property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }
}