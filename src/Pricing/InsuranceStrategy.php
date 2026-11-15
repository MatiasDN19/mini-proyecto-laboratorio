<?php

/**
 * Paciente con obra social: descuento del 30%.
 * El porcentaje vive en un unico lugar del sistema: esta clase.
 */
final class InsuranceStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.30)
    {
        if ($this->discount < 0 || $this->discount > 1) {
            throw new InvalidArgumentException('El descuento debe estar entre 0 y 1.');
        }
    }

    public function calculate(float $amount): float
    {
        return $amount * (1 - $this->discount);
    }
}
