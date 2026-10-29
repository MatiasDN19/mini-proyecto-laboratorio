<?php
/**
 * ============================================================================
 *  CALCULO DE PRECIOS — Patron STRATEGY
 * ============================================================================
 *
 *  PriceCalculator no conoce los algoritmos de precio: recibe una
 *  PricingStrategy por constructor y le delega el calculo (composicion).
 *  Agregar un tipo de paciente = agregar una clase que implemente
 *  PricingStrategy. Esta clase no se modifica.
 * ============================================================================
 */

final class PriceCalculator
{
    public function __construct(private PricingStrategy $strategy) {}

    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }

    /**
     * ❌ METODO MAL APLICADO: calcularConIva()
     *    Duplica la logica de arriba "para no tocar lo que ya anda".
     *    Cuando cambie el descuento de obra social, hay que acordarse de los DOS lugares.
     */
    public function calcularConIva(float $monto, string $tipoPaciente): float
    {
        if ($tipoPaciente === 'obra_social') {
            return ($monto * 0.7) * 1.21;
        }

        return $monto * 1.21;
    }
}