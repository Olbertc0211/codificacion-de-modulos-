package com.example.suplementor;

import org.junit.Test;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.fail;

public class ImcCalculatorTest {

    // Comprueba la fórmula usando peso en kg y estatura en centímetros.
    @Test
    public void calculatesBodyMassIndexFromKilogramsAndCentimeters() {
        assertEquals(22.86, ImcCalculator.calculate(70, 175), 0.01);
    }

    // Verifica las categorías orientativas en distintos rangos del IMC.
    @Test
    public void classifiesBodyMassIndexRanges() {
        assertEquals("Bajo peso", ImcCalculator.category(18.4));
        assertEquals("Rango considerado normal", ImcCalculator.category(22));
        assertEquals("Sobrepeso", ImcCalculator.category(27));
        assertEquals("Obesidad", ImcCalculator.category(31));
    }

    // Confirma que las medidas cero o negativas se rechazan.
    @Test
    public void rejectsZeroOrNegativeMeasurements() {
        assertInvalidMeasurement(0, 175);
        assertInvalidMeasurement(70, -1);
    }

    // Ayudante para comprobar el rechazo y el mensaje de medidas inválidas.
    private void assertInvalidMeasurement(double weightKg, double heightCm) {
        try {
            ImcCalculator.calculate(weightKg, heightCm);
            fail("Expected invalid measurements to be rejected.");
        } catch (IllegalArgumentException expected) {
            assertEquals(
                    "El peso y la estatura deben ser mayores que cero.",
                    expected.getMessage()
            );
        }
    }
}
