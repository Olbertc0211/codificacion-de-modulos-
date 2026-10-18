# Aplicación móvil Suplementor

Aplicación nativa para Android desarrollada en Java. Incluye bienvenida, registro e inicio de sesión mediante la API PHP `mobile-api/` y una calculadora de índice de masa corporal (IMC), que aparece después de iniciar sesión correctamente.

## Requisitos

- Android Studio con el Android SDK indicado por el proyecto.
- JDK compatible con Gradle 9.4.1.
- XAMPP con Apache y MySQL para el backend de registro/login.
- MySQL Workbench para crear la base móvil.

## Preparar y compilar

1. Sigue [`../mobile-api/README.md`](../mobile-api/README.md) para preparar `suplementormovil` y dejar la API disponible en Apache.
2. Abre esta carpeta (`android-app`) en Android Studio y espera a que termine la sincronización de Gradle.
3. En `app/src/main/java/com/example/suplementor/LoginActivity.java`, configura `BASE_URL` con la IP Wi-Fi del computador que ejecuta XAMPP. Para un teléfono físico, ambos deben estar en la misma red; para el emulador, usa `http://10.0.2.2/mi_api/`.
4. Ejecuta la configuración `app` en un emulador o dispositivo conectado.

También se puede compilar desde PowerShell en esta carpeta:

```powershell
.\gradlew.bat testDebugUnitTest assembleDebug
```

El APK de depuración se genera en `app/build/outputs/apk/debug/app-debug.apk`; los APK y directorios `build` no se guardan en Git.

## Calculadora de IMC

La calculadora pide peso en kilogramos y estatura en centímetros. Usa la fórmula `peso / estatura_en_metros²`, valida medidas positivas y presenta una categoría orientativa. El resultado es informativo y no sustituye una evaluación profesional.
