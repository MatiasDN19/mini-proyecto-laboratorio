<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Pruebas de las estrategias con descuento (obra social y jubilado)
 * y del calculo con IVA. Cada metodo indica su caso en docs/CASOS-DE-PRUEBA.md.
 */
final class DiscountStrategiesTest extends TestCase
{
    /** CP-06 (positivo): obra social aplica el 30% de descuento por defecto. */
    public function testObraSocialAplicaTreintaPorCientoDeDescuento(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(10500.0, $calculadora->calculate(15000), 0.001);
    }

    /** CP-07 (positivo): el descuento de obra social es configurable. */
    public function testObraSocialAceptaUnDescuentoConfigurable(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy(0.25));

        $this->assertEqualsWithDelta(11250.0, $calculadora->calculate(15000), 0.001);
    }

    /** CP-08 (negativo): un descuento mayor al 100% se rechaza. */
    public function testObraSocialRechazaUnDescuentoMayorAlCienPorCiento(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El descuento debe estar entre 0 y 1.');

        new InsuranceStrategy(1.5);
    }

    /** CP-09 (positivo): jubilado aplica el 50% de descuento por defecto. */
    public function testJubiladoAplicaCincuentaPorCientoDeDescuento(): void
    {
        $calculadora = new PriceCalculator(new RetiredStrategy());

        $this->assertEqualsWithDelta(7500.0, $calculadora->calculate(15000), 0.001);
    }

    /** CP-10 (negativo): un descuento negativo se rechaza. */
    public function testJubiladoRechazaUnDescuentoNegativo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El descuento debe estar entre 0 y 1.');

        new RetiredStrategy(-0.10);
    }

    /** CP-11 (positivo): el IVA se aplica sobre el precio con descuento. */
    public function testObraSocialConIvaSeCalculaSobreElPrecioConDescuento(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(12705.0, $calculadora->calculateWithIva(15000), 0.001);
    }

    /**
     * CP-12 (positivo, regresion): jubilado con IVA respeta su descuento.
     * Antes del refactor, calcularConIva() cobraba 18150 (sin el 50%).
     */
    public function testJubiladoConIvaRespetaSuDescuento(): void
    {
        $calculadora = new PriceCalculator(new RetiredStrategy());

        $this->assertEqualsWithDelta(9075.0, $calculadora->calculateWithIva(15000), 0.001);
    }

    /** CP-13 (negativo): el calculo con IVA tambien rechaza montos negativos. */
    public function testCalculoConIvaRechazaUnMontoNegativo(): void
    {
        $calculadora = new PriceCalculator(new InsuranceStrategy());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El monto no puede ser negativo.');

        $calculadora->calculateWithIva(-500);
    }
}
