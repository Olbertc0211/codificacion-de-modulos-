package com.example.suplementor;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Muestra la pantalla de bienvenida definida en activity_main.xml.
        setContentView(R.layout.activity_main);

        // Busca el botón de acceso dentro del diseño de la pantalla.
        Button btnIniciarSesion = findViewById(R.id.btnIniciarSesion);

        // Abre la pantalla donde el usuario puede registrarse o iniciar sesión.
        btnIniciarSesion.setOnClickListener(v -> {
            Intent intent = new Intent(MainActivity.this, LoginActivity.class);
            startActivity(intent);
        });
    }
}