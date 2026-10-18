package com.example.suplementor;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;
import com.android.volley.ParseError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.ServerError;
import com.android.volley.TimeoutError;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONException;
import org.json.JSONObject;
import java.nio.charset.StandardCharsets;

public class LoginActivity extends AppCompatActivity {

    // Campos y mensajes que se conectan con los controles del diseño XML.
    private EditText etNombre, etEmail, etPassword;
    private TextView tvResultado;
    private Button btnRegistrar, btnLogin;

    // El celular y el computador con XAMPP deben estar en la misma red Wi-Fi.
    // Actualiza la IP cuando cambie la dirección local del computador.
    private static final String BASE_URL = "http://192.168.100.18/mi_api/";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Carga el formulario y obtiene referencias a sus campos y botones.
        setContentView(R.layout.activity_login);

        etNombre = findViewById(R.id.etNombre);
        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        tvResultado = findViewById(R.id.tvResultado);
        btnRegistrar = findViewById(R.id.btnRegistrar);
        btnLogin = findViewById(R.id.btnLogin);

        btnRegistrar.setOnClickListener(v -> registrarUsuario());
        btnLogin.setOnClickListener(v -> loginUsuario());
    }

    // Valida los campos y envía el nuevo usuario al endpoint PHP de registro.
    private void registrarUsuario() {
        String nombre = etNombre.getText().toString().trim();
        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        // Evita enviar una solicitud si falta alguno de los datos requeridos.
        if (nombre.isEmpty() || email.isEmpty() || password.isEmpty()) {
            tvResultado.setText("Completa todos los campos");
            return;
        }

        String url = BASE_URL + "registro.php";
        RequestQueue queue = Volley.newRequestQueue(this);
        JSONObject params = new JSONObject();
        try {
            // El servidor PHP espera estos tres campos en formato JSON.
            params.put("nombre", nombre);
            params.put("email", email);
            params.put("password", password);
        } catch (JSONException e) { e.printStackTrace(); }

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, params,
                response -> {
                    try {
                        boolean success = response.getBoolean("success");
                        // Muestra el resultado que devolvió el servidor.
                        tvResultado.setText(response.getString("message"));
                        if (success) {
                            // Limpia el formulario después de un registro exitoso.
                            etNombre.setText("");
                            etEmail.setText("");
                            etPassword.setText("");
                        }
                    } catch (JSONException e) { e.printStackTrace(); }
                },
                error -> {
                    tvResultado.setText(formatNetworkError("registrar el usuario", error));
                });
        queue.add(request);
    }

    // Envía las credenciales al servidor y abre la calculadora cuando son válidas.
    private void loginUsuario() {
        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        // Comprueba localmente que ambos campos tengan contenido.
        if (email.isEmpty() || password.isEmpty()) {
            tvResultado.setText("Completa email y contraseña");
            return;
        }

        String url = BASE_URL + "login.php";
        RequestQueue queue = Volley.newRequestQueue(this);
        JSONObject params = new JSONObject();
        try {
            // El endpoint de acceso recibe el correo y la contraseña como JSON.
            params.put("email", email);
            params.put("password", password);
        } catch (JSONException e) { e.printStackTrace(); }

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, params,
                response -> {
                    try {
                        boolean success = response.getBoolean("success");
                        if (success) {
                            String user = response.getString("user");
                            // Pasa el nombre del usuario a la pantalla de cálculo de IMC.
                            Intent intent = new Intent(LoginActivity.this, ImcActivity.class);
                            intent.putExtra(ImcActivity.EXTRA_USER_NAME, user);
                            startActivity(intent);
                            // Evita volver al formulario con el botón Atrás tras entrar.
                            finish();
                        } else {
                            tvResultado.setText("❌ " + response.getString("message"));
                        }
                    } catch (JSONException e) { e.printStackTrace(); }
                },
                error -> {
                    tvResultado.setText(formatNetworkError("iniciar sesión", error));
                });
        queue.add(request);
    }

    // Convierte los errores HTTP o de red de Volley en mensajes comprensibles.
    private String formatNetworkError(String action, VolleyError error) {
        if (error.networkResponse != null) {
            byte[] responseData = error.networkResponse.data;
            if (responseData != null) {
                try {
                    // Si PHP devolvió JSON, presenta su mensaje específico.
                    JSONObject response = new JSONObject(new String(responseData, StandardCharsets.UTF_8));
                    String message = response.optString("message", "");
                    if (!message.isEmpty()) {
                        return message;
                    }
                } catch (JSONException ignored) {
                    // Si la respuesta no es JSON, se informa el código HTTP más abajo.
                }
            }

            // La respuesta del servidor no contiene un mensaje utilizable.
            return "No se pudo " + action + " (HTTP "
                    + error.networkResponse.statusCode + "). Verifica los datos e inténtalo de nuevo.";
        }

        // Distingue el tiempo de espera de los problemas de formato y conectividad.
        if (error instanceof TimeoutError) {
            return "El servidor tardó demasiado en responder. Inténtalo de nuevo.";
        }
        if (error instanceof ParseError || error instanceof ServerError) {
            return "El servidor devolvió una respuesta inválida. Revisa el servicio e inténtalo de nuevo.";
        }

        return "No se pudo conectar con el servidor. Comprueba que Apache y MySQL estén iniciados y que el teléfono esté en la misma red.";
    }
}