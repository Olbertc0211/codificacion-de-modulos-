/*
 * Módulo front-end de Suplementor.
 * Centraliza el estado de la sesión de compra y las vistas modales
 * para mantener una interfaz consistente y fácil de ampliar.
 */

const elementos = {
    modal: document.getElementById("modal"),
    tituloModal: document.getElementById("tituloModal"),
    contenidoModal: document.getElementById("contenidoModal"),
    loginModal: document.getElementById("loginModal"),
    compraModal: document.getElementById("compraModal"),
    usuariosModal: document.getElementById("usuariosModal"),
    reportesModal: document.getElementById("reportesModal"),
    inicioModal: document.getElementById("inicioModal")
};

const productos = [
    { nombre: "Proteína Whey", descripcion: "Proteína de rápida absorción.", icono: "fa-glass-water", clase: "whey" },
    { nombre: "Creatina", descripcion: "Mayor fuerza y potencia.", icono: "fa-bolt", clase: "creatina" },
    { nombre: "Pre Entreno", descripcion: "Más energía para entrenar.", icono: "fa-fire", clase: "pre" },
    { nombre: "Hypercalórica", descripcion: "Ideal para aumentar masa.", icono: "fa-burger", clase: "hyper" },
    { nombre: "BCAA", descripcion: "Aminoácidos esenciales.", icono: "fa-capsules", clase: "bcaa" },
    { nombre: "Glutamina", descripcion: "Ayuda a la recuperación.", icono: "fa-flask", clase: "glutamina" }
];

const marcas = [
    { nombre: "Optimum Nutrition", icono: "fa-medal" },
    { nombre: "Rule One", icono: "fa-award" },
    { nombre: "Dymatize", icono: "fa-shield" },
    { nombre: "BSN", icono: "fa-star" },
    { nombre: "MuscleTech", icono: "fa-gem" },
    { nombre: "Kevin Levrone", icono: "fa-trophy" }
];

const precioProducto = 100000;
let carrito = cargarCarrito();

/**
 * Recupera el carrito de la sesión del navegador para no perderlo al cerrar
 * accidentalmente un modal o actualizar la página.
 */
function cargarCarrito() {
    try {
        const carritoGuardado = sessionStorage.getItem("suplementor-carrito");
        const carritoParseado = carritoGuardado ? JSON.parse(carritoGuardado) : [];
        return Array.isArray(carritoParseado) ? carritoParseado : [];
    } catch (error) {
        console.error("No fue posible recuperar el carrito:", error);
        return [];
    }
}

function guardarCarrito() {
    sessionStorage.setItem("suplementor-carrito", JSON.stringify(carrito));
    actualizarContadorCarrito();
}

function actualizarContadorCarrito() {
    const contador = document.getElementById("cartCount");
    if (contador) {
        const cantidad = carrito.length;
        contador.textContent = `${cantidad} ${cantidad === 1 ? "producto" : "productos"}`;
    }
}

function abrirModal(titulo, contenido) {
    elementos.tituloModal.textContent = titulo;
    elementos.contenidoModal.innerHTML = contenido;
    elementos.modal.classList.remove("hidden");
}

function cerrarModal() {
    elementos.modal.classList.add("hidden");
}

function abrirLogin() {
    elementos.loginModal.classList.remove("hidden");
    document.getElementById("email").focus();
}

function cerrarLogin() {
    elementos.loginModal.classList.add("hidden");
}

/**
 * Envía las credenciales al endpoint PHP y redirige cuando son válidas.
 */
async function login() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const mensaje = document.getElementById("loginMessage");
    const boton = document.querySelector("#loginForm button[type='submit']");

    if (!email || !password) {
        mensaje.textContent = "Por favor completa todos los campos.";
        return;
    }

    mensaje.textContent = "Validando credenciales...";
    boton.disabled = true;

    const datos = new FormData();
    datos.append("correo", email);
    datos.append("password", password);

    try {
        const respuesta = await fetch("login.php", { method: "POST", body: datos });
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status}`);
        }

        const resultado = (await respuesta.text()).trim();
        const mensajes = {
            contraseña: "La contraseña es incorrecta.",
            usuario: "El correo electrónico no está registrado."
        };

        if (resultado === "ok") {
            cerrarLogin();
            alert("Inicio de sesión exitoso.");
            window.location.href = "panel.php";
            return;
        }

        mensaje.textContent = mensajes[resultado] || "Ocurrió un error al iniciar sesión.";
    } catch (error) {
        console.error("No fue posible iniciar sesión:", error);
        mensaje.textContent = window.location.protocol === "file:"
            ? "Abre el sistema desde http://localhost/suplementor/index.html, no desde un archivo local."
            : "No se pudo conectar con el servidor. Verifica que Apache esté encendido.";
    } finally {
        boton.disabled = false;
    }
}

function mostrarProductos() {
    const tarjetas = productos.map((producto) => `
        <article class="card ${producto.clase}">
            <i class="fa-solid ${producto.icono}" aria-hidden="true"></i>
            <h3>${producto.nombre}</h3>
            <p>${producto.descripcion}</p>
            <button type="button" onclick="agregarCarrito('${producto.nombre}')">Agregar</button>
        </article>
    `).join("");

    abrirModal("Catálogo de Productos", `<div class="cards-grid">${tarjetas}</div>`);
}

function agregarCarrito(nombreProducto) {
    const producto = productos.find(({ nombre }) => nombre === nombreProducto);
    if (!producto) {
        console.error("Producto no encontrado:", nombreProducto);
        return;
    }

    carrito.push(producto.nombre);
    guardarCarrito();
    alert(`${producto.nombre} agregado al carrito correctamente.`);
}

function mostrarInventario() {
    const tarjetas = marcas.map((marca) => `
        <article class="card">
            <i class="fa-solid ${marca.icono}" aria-hidden="true"></i>
            <h3>${marca.nombre}</h3>
            <p>Stock disponible</p>
        </article>
    `).join("");

    abrirModal("Marcas Disponibles", `<div class="cards-grid">${tarjetas}</div>`);
}

function mostrarVentas() {
    if (carrito.length === 0) {
        abrirModal("Carrito", `
            <h3>No hay productos agregados.</h3>
            <p>Agrega un producto desde el catálogo.</p>
        `);
        return;
    }

    const tarjetas = carrito.map((producto, indice) => `
        <article class="card">
            <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
            <h3>${producto}</h3>
            <p>${formatearPrecio(precioProducto)}</p>
            <button type="button" onclick="eliminarProducto(${indice})">Eliminar</button>
        </article>
    `).join("");

    const total = carrito.length * precioProducto;
    abrirModal("Carrito de Compras", `
        <div class="cards-grid">${tarjetas}</div>
        <p class="carrito-total">Total: ${formatearPrecio(total)}</p>
        <button type="button" onclick="comprar()">Finalizar Compra</button>
    `);
}

function eliminarProducto(indice) {
    if (indice < 0 || indice >= carrito.length) {
        return;
    }

    carrito.splice(indice, 1);
    guardarCarrito();
    mostrarVentas();
}

function comprar() {
    carrito = [];
    cerrarModal();
    elementos.compraModal.classList.remove("hidden");
}

function cerrarCompra() {
    elementos.compraModal.classList.add("hidden");
}

function formatearPrecio(valor) {
    return `$${valor.toLocaleString("es-CO")}`;
}

function mostrarUsuarios() {
    elementos.usuariosModal.classList.remove("hidden");
}

function cerrarUsuarios() {
    elementos.usuariosModal.classList.add("hidden");
}

function mostrarReportes() {
    elementos.reportesModal.classList.remove("hidden");
}

function cerrarReportes() {
    elementos.reportesModal.classList.add("hidden");
}

function mostrarInicio() {
    elementos.inicioModal.classList.remove("hidden");
}

function cerrarInicio() {
    elementos.inicioModal.classList.add("hidden");
}

function cerrarModalActivo() {
    cerrarModal();
    cerrarLogin();
    cerrarCompra();
    cerrarUsuarios();
    cerrarReportes();
    cerrarInicio();
}

function abrirRedSocial(nombre, url) {
    const salir = confirm(
        `Estás a punto de salir de Suplementor para visitar ${nombre}.\n\n¿Deseas continuar?`
    );

    if (salir) {
        window.open(url, "_blank", "noopener,noreferrer");
    }
}

// Permite cerrar cualquier ventana modal con Escape o haciendo clic en su fondo.
document.addEventListener("keydown", (evento) => {
    if (evento.key === "Escape") {
        cerrarModalActivo();
    }
});

document.addEventListener("click", (evento) => {
    const enlace = evento.target.closest("a[href='#']");
    if (enlace) {
        evento.preventDefault();
    }

    const modales = Object.values(elementos).filter((elemento) =>
        elemento && elemento.classList && elemento.classList.contains("modal")
    );

    if (modales.includes(evento.target)) {
        evento.target.classList.add("hidden");
    }
});

document.getElementById("loginForm").addEventListener("submit", (evento) => {
    evento.preventDefault();
    login();
});

// El contador se sincroniza al cargar la vista con el estado de la sesión.
actualizarContadorCarrito();
