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
