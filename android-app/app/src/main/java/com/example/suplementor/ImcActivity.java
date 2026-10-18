package com.example.suplementor;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;

public class ImcActivity extends AppCompatActivity {

    // Clave usada para recibir el nombre enviado desde LoginActivity.
    public static final String EXTRA_USER_NAME = "com.example.suplementor.USER_NAME";

    // Controles del formulario y de la presentación del resultado.
    private EditText etWeight;
    private EditText etHeight;
    private TextView tvWelcome;
    private TextView tvResult;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Carga la pantalla de la calculadora definida en XML.
        setContentView(R.layout.activity_imc);

        etWeight = findViewById(R.id.etWeight);
        etHeight = findViewById(R.id.etHeight);
        tvWelcome = findViewById(R.id.tvWelcome);
        tvResult = findViewById(R.id.tvImcResult);
        Button btnCalculate = findViewById(R.id.btnCalculateImc);
        Button btnLogout = findViewById(R.id.btnImcLogout);

        // Personaliza el saludo si LoginActivity entregó un nombre.
        String userName = getIntent().getStringExtra(EXTRA_USER_NAME);
        if (userName != null && !userName.trim().isEmpty()) {
            tvWelcome.setText(getString(R.string.imc_welcome, userName));
        }

        // Conecta las acciones principales de la pantalla.
        btnCalculate.setOnClickListener(view -> calculateImc());
        btnLogout.setOnClickListener(view -> finish());
    }

    // Valida peso y estatura, calcula el IMC y muestra una categoría orientativa.
    private void calculateImc() {
        // Acepta tanto punto como coma decimal, según la configuración del teclado.
        String weightText = etWeight.getText().toString().trim().replace(',', '.');
        String heightText = etHeight.getText().toString().trim().replace(',', '.');

        if (weightText.isEmpty()) {
            etWeight.setError(getString(R.string.imc_weight_required));
            return;
        }
        if (heightText.isEmpty()) {
            etHeight.setError(getString(R.string.imc_height_required));
            return;
        }

        try {
            // El usuario ingresa el peso en kg y la estatura en centímetros.
            double weightKg = Double.parseDouble(weightText);
            double heightCm = Double.parseDouble(heightText);
            double bmi = ImcCalculator.calculate(weightKg, heightCm);

            // Presenta el resultado con un decimal y su rango orientativo.
            tvResult.setText(getString(
                    R.string.imc_result,
                    ImcCalculator.format(bmi),
                    ImcCalculator.category(bmi)
            ));
        } catch (NumberFormatException exception) {
            // El contenido no pudo convertirse a un número.
            tvResult.setText(R.string.imc_invalid_number);
        } catch (IllegalArgumentException exception) {
            // La calculadora rechazó una medida igual o menor que cero.
            tvResult.setText(R.string.imc_positive_values);
        }
    }
}
