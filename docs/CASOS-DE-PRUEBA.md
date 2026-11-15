# Casos de prueba — Cálculo de precios (patrón Strategy)

Este documento describe los casos de prueba del código refactorizado en `src/Pricing/`. Cada caso está automatizado con PHPUnit: el campo **Prueba automatizada** indica el método que lo ejecuta.

Para correrlos:

```
composer install
vendor/bin/phpunit --testdox
```

Cada funcionalidad tiene al menos un caso **positivo** (camino feliz: datos válidos, el requisito se cumple) y uno **negativo** (camino alternativo: datos inválidos, el sistema tiene que responder bien al error).

---

## Funcionalidad 1: Cálculo de precio para paciente particular

### CP-01 · Paciente particular paga el monto completo

- **Tipo:** positivo
- **Descripción:** verifica que un paciente particular no recibe ningún descuento.
- **Precondiciones:** existe una `PriceCalculator` creada con `PrivatePatientStrategy`.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new PrivatePatientStrategy())`.
  2. Llamar a `calculate(15000)`.
- **Resultado esperado:** devuelve 15000.
- **Prioridad:** alta
- **Prueba automatizada:** `PriceCalculatorTest::testPacienteParticularPagaElMontoCompleto`

### CP-02 · Se rechaza un monto negativo

- **Tipo:** negativo
- **Descripción:** verifica que la calculadora no acepta montos negativos, que no tienen sentido para un pedido.
- **Precondiciones:** existe una `PriceCalculator` creada con `PrivatePatientStrategy`.
- **Datos de entrada:** monto = -100
- **Pasos:**
  1. Crear `new PriceCalculator(new PrivatePatientStrategy())`.
  2. Llamar a `calculate(-100)`.
- **Resultado esperado:** se lanza `InvalidArgumentException` con el mensaje "El monto no puede ser negativo."
- **Prioridad:** alta
- **Prueba automatizada:** `PriceCalculatorTest::testRechazaUnMontoNegativo`

### CP-03 · Un monto de cero es válido

- **Tipo:** positivo (valor límite)
- **Descripción:** verifica el borde de la validación: cero es el menor monto permitido.
- **Precondiciones:** existe una `PriceCalculator` creada con `PrivatePatientStrategy`.
- **Datos de entrada:** monto = 0
- **Pasos:**
  1. Crear `new PriceCalculator(new PrivatePatientStrategy())`.
  2. Llamar a `calculate(0)`.
- **Resultado esperado:** devuelve 0, sin lanzar excepción.
- **Prioridad:** media
- **Prueba automatizada:** `PriceCalculatorTest::testMontoCeroDevuelveCero`

## Funcionalidad 2: Delegación en la estrategia (contrato `PricingStrategy`)

### CP-04 · Una estrategia nueva se usa sin modificar la calculadora

- **Tipo:** positivo
- **Descripción:** verifica el principio Abierto/Cerrado: se agrega un tipo de paciente nuevo (una "prepaga" con 20% de descuento) sin tocar `PriceCalculator`.
- **Precondiciones:** existe una clase que implementa `PricingStrategy` y descuenta el 20%.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear una estrategia "prepaga" que implemente `PricingStrategy`.
  2. Crear `new PriceCalculator($prepaga)`.
  3. Llamar a `calculate(15000)`.
- **Resultado esperado:** devuelve 12000.
- **Prioridad:** alta
- **Prueba automatizada:** `PriceCalculatorTest::testAceptaUnaEstrategiaNuevaSinModificarLaCalculadora`

### CP-05 · No se puede crear la calculadora sin estrategia

- **Tipo:** negativo
- **Descripción:** verifica que el contrato se cumple: la calculadora exige una `PricingStrategy` y no acepta un valor nulo.
- **Precondiciones:** ninguna.
- **Datos de entrada:** estrategia = `null`
- **Pasos:**
  1. Intentar crear `new PriceCalculator(null)`.
- **Resultado esperado:** se lanza `TypeError`.
- **Prioridad:** media
- **Prueba automatizada:** `PriceCalculatorTest::testNoSePuedeCrearSinEstrategia`

## Funcionalidad 3: Descuento para paciente con obra social

### CP-06 · Obra social aplica el 30% de descuento

- **Tipo:** positivo
- **Descripción:** verifica el descuento por defecto de obra social.
- **Precondiciones:** existe una `PriceCalculator` creada con `InsuranceStrategy` sin parámetros.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new InsuranceStrategy())`.
  2. Llamar a `calculate(15000)`.
- **Resultado esperado:** devuelve 10500.
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testObraSocialAplicaTreintaPorCientoDeDescuento`

### CP-07 · El descuento de obra social es configurable

- **Tipo:** positivo
- **Descripción:** verifica que otra obra social con otro porcentaje se resuelve sin crear una clase nueva.
- **Precondiciones:** existe una `PriceCalculator` creada con `InsuranceStrategy(0.25)`.
- **Datos de entrada:** descuento = 0.25; monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new InsuranceStrategy(0.25))`.
  2. Llamar a `calculate(15000)`.
- **Resultado esperado:** devuelve 11250.
- **Prioridad:** media
- **Prueba automatizada:** `DiscountStrategiesTest::testObraSocialAceptaUnDescuentoConfigurable`

### CP-08 · Se rechaza un descuento mayor al 100%

- **Tipo:** negativo
- **Descripción:** verifica que no se puede crear una obra social con un descuento imposible (daría precios negativos).
- **Precondiciones:** ninguna.
- **Datos de entrada:** descuento = 1.5
- **Pasos:**
  1. Intentar crear `new InsuranceStrategy(1.5)`.
- **Resultado esperado:** se lanza `InvalidArgumentException` con el mensaje "El descuento debe estar entre 0 y 1."
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testObraSocialRechazaUnDescuentoMayorAlCienPorCiento`

## Funcionalidad 4: Descuento para paciente jubilado

### CP-09 · Jubilado aplica el 50% de descuento

- **Tipo:** positivo
- **Descripción:** verifica el descuento por defecto de jubilado.
- **Precondiciones:** existe una `PriceCalculator` creada con `RetiredStrategy` sin parámetros.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new RetiredStrategy())`.
  2. Llamar a `calculate(15000)`.
- **Resultado esperado:** devuelve 7500.
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testJubiladoAplicaCincuentaPorCientoDeDescuento`

### CP-10 · Se rechaza un descuento negativo

- **Tipo:** negativo
- **Descripción:** verifica que no se puede crear un descuento negativo (encarecería el precio).
- **Precondiciones:** ninguna.
- **Datos de entrada:** descuento = -0.10
- **Pasos:**
  1. Intentar crear `new RetiredStrategy(-0.10)`.
- **Resultado esperado:** se lanza `InvalidArgumentException` con el mensaje "El descuento debe estar entre 0 y 1."
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testJubiladoRechazaUnDescuentoNegativo`

## Funcionalidad 5: Cálculo con IVA

### CP-11 · El IVA se aplica sobre el precio con descuento

- **Tipo:** positivo
- **Descripción:** verifica que primero se aplica el descuento de la estrategia y después el IVA del 21%.
- **Precondiciones:** existe una `PriceCalculator` creada con `InsuranceStrategy`.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new InsuranceStrategy())`.
  2. Llamar a `calculateWithIva(15000)`.
- **Resultado esperado:** devuelve 12705 (10500 × 1,21).
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testObraSocialConIvaSeCalculaSobreElPrecioConDescuento`

### CP-12 · Jubilado con IVA respeta su descuento (regresión)

- **Tipo:** positivo · prueba de regresión
- **Descripción:** verifica que no vuelva el bug que tenía el código original: `calcularConIva()` se olvidaba del descuento de jubilado y cobraba 18150.
- **Precondiciones:** existe una `PriceCalculator` creada con `RetiredStrategy`.
- **Datos de entrada:** monto = 15000
- **Pasos:**
  1. Crear `new PriceCalculator(new RetiredStrategy())`.
  2. Llamar a `calculateWithIva(15000)`.
- **Resultado esperado:** devuelve 9075 (7500 × 1,21), no 18150.
- **Prioridad:** alta
- **Prueba automatizada:** `DiscountStrategiesTest::testJubiladoConIvaRespetaSuDescuento`

### CP-13 · El cálculo con IVA rechaza un monto negativo

- **Tipo:** negativo
- **Descripción:** verifica que la validación del monto también protege al cálculo con IVA.
- **Precondiciones:** existe una `PriceCalculator` creada con `InsuranceStrategy`.
- **Datos de entrada:** monto = -500
- **Pasos:**
  1. Crear `new PriceCalculator(new InsuranceStrategy())`.
  2. Llamar a `calculateWithIva(-500)`.
- **Resultado esperado:** se lanza `InvalidArgumentException` con el mensaje "El monto no puede ser negativo."
- **Prioridad:** media
- **Prueba automatizada:** `DiscountStrategiesTest::testCalculoConIvaRechazaUnMontoNegativo`
