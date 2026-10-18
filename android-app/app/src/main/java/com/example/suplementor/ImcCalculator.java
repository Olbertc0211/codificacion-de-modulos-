package com.example.suplementor;

import java.util.Locale;

// Mantiene las operaciones matemáticas del IMC separadas de la interfaz.
public final class ImcCalculator {

    // Esta clase contiene solo métodos estáticos; no requiere instancias.
    private ImcCalculator() {}

    // Calcula el índice con la fórmula: peso en kg / estatura en metros al cuadrado.
    public static double calculate(double weightKg, double heightCm) {
        // Impide cálculos con datos infinitos, no numéricos o no positivos.
        if (!Double.isFinite(weightKg) || !Double.isFinite(heightCm)
                || weightKg <= 0 || heightCm <= 0) {
            throw new IllegalArgumentException("El peso y la estatura deben ser mayores que cero.");
        }

        // La interfaz recibe centímetros; la fórmula utiliza metros.
        double heightMeters = heightCm / 100.0;
        return weightKg / (heightMeters * heightMeters);
    }

    // Devuelve una categoría general basada en el valor calculado.
    public static String category(double bmi) {
        if (!Double.isFinite(bmi) || bmi <= 0) {
            throw new IllegalArgumentException("El IMC debe ser un número positivo.");
        }
        if (bmi < 18.5) {
            return "Bajo peso";
        }
        if (bmi < 25) {
            return "Rango considerado normal";
        }
        if (bmi < 30) {
            return "Sobrepeso";
        }
        return "Obesidad";
    }

    // Limita la presentación del resultado a un decimal.
    public static String format(double bmi) {
        return String.format(Locale.getDefault(), "%.1f", bmi);
    }
}
