<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Pruebas de PriceCalculator: el "contexto" del patron Strategy.
 * Cada metodo indica el caso documentado en docs/CASOS-DE-PRUEBA.md.
 */
final class PriceCalculatorTest extends TestCase
{
    /** CP-01 (positivo): un paciente particular paga el monto completo. */
    public function testPacienteParticularPagaElMontoCompleto(): void
    {
        $calculadora = new PriceCalculator(new PrivatePatientStrategy());

        $this->assertEqualsWithDelta(15000.0, $calculadora->calculate(15000), 0.001);
    }

    /** CP-02 (negativo): un monto negativo se rechaza con una excepcion. */
    public function testRechazaUnMontoNegativo(): void
    {
        $calculadora = new PriceCalculator(new PrivatePatientStrategy());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El monto no puede ser negativo.');

        $calculadora->calculate(-100);
    }

    /** CP-03 (positivo, valor limite): un monto de cero es valido y da cero. */
    public function testMontoCeroDevuelveCero(): void
    {
        $calculadora = new PriceCalculator(new PrivatePatientStrategy());

        $this->assertEqualsWithDelta(0.0, $calculadora->calculate(0), 0.001);
    }

    /**
     * CP-04 (positivo): una estrategia nueva se usa sin modificar PriceCalculator.
     * Simula una "prepaga" con 20% de descuento: demuestra Abierto/Cerrado.
     */
    public function testAceptaUnaEstrategiaNuevaSinModificarLaCalculadora(): void
    {
        $prepaga = new class implements PricingStrategy {
            public function calculate(float $amount): float
            {
                return $amount * 0.80;
            }
        };

        $calculadora = new PriceCalculator($prepaga);

        $this->assertEqualsWithDelta(12000.0, $calculadora->calculate(15000), 0.001);
    }

    /** CP-05 (negativo): no se puede crear la calculadora sin una estrategia. */
    public function testNoSePuedeCrearSinEstrategia(): void
    {
        $this->expectException(TypeError::class);

        new PriceCalculator(null);
    }
}
