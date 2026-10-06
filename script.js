/*
 * Conecta la interfaz de Suplementor con la API Laravel.
 * El token permanece solo en sessionStorage y se elimina al cerrar sesión.
 */
const API_BASE_URL = "http://127.0.0.1:8000/api";
const TOKEN_KEY = "suplementor-api-token";
const CART_KEY = "suplementor-carrito";

const elementos = {
    modal: document.getElementById("modal"),
    tituloModal: document.getElementById("tituloModal"),
    contenidoModal: document.getElementById("contenidoModal"),
    loginModal: document.getElementById("loginModal"),
    compraModal: document.getElementById("compraModal"),
    usuariosModal: document.getElementById("usuariosModal"),
    reportesModal: document.getElementById("reportesModal"),
    inicioModal: document.getElementById("inicioModal"),
    loginMessage: document.getElementById("loginMessage"),
    logoutLink: document.getElementById("logoutLink")
};

const iconosProducto = [
    "fa-glass-water",
    "fa-bolt",
    "fa-fire",
    "fa-burger",
    "fa-capsules",
    "fa-flask"
];

let usuarioActual = null;
let carrito = cargarCarrito();

function cargarCarrito() {
    try {
        const guardado = sessionStorage.getItem(CART_KEY);
        const items = guardado ? JSON.parse(guardado) : [];
        return Array.isArray(items) ? items.filter((item) =>
            item &&
            Number.isInteger(item.producto_id) &&
            typeof item.nombre === "string" &&
            Number.isFinite(item.precio) &&
            Number.isInteger(item.cantidad) &&
            item.cantidad > 0
        ) : [];
    } catch (error) {
        console.error("No fue posible recuperar el carrito:", error);
        return [];
    }
}

function guardarCarrito() {
    sessionStorage.setItem(CART_KEY, JSON.stringify(carrito));
    actualizarContadorCarrito();
}

function actualizarContadorCarrito() {
    const contador = document.getElementById("cartCount");
    const cantidad = carrito.reduce((total, item) => total + item.cantidad, 0);

    if (contador) {
        contador.textContent = `${cantidad} ${cantidad === 1 ? "producto" : "productos"}`;
    }
}

function escaparHTML(valor) {
    return String(valor).replace(/[&<>"']/g, (caracter) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "\"": "&quot;",
        "'": "&#39;"
    })[caracter]);
}

function mensajeError(error) {
    if (error.status === 0) {
        return "No se pudo conectar con Laravel. Verifica PHP 8.3, ejecuta `php artisan serve` en laravel-backend y revisa que Apache esté abierto.";
    }

    if (error.status === 401) {
        return "Tu sesión venció. Inicia sesión nuevamente.";
    }

    if (error.status === 403) {
        return "Tu usuario no tiene permisos para realizar esta acción.";
    }

    if (error.status === 422 && error.data && error.data.errors) {
        return Object.values(error.data.errors).flat().join(" ");
    }

    return (error.data && error.data.message) || "No fue posible completar la solicitud.";
}

async function apiRequest(ruta, opciones = {}) {
    const token = sessionStorage.getItem(TOKEN_KEY);
    const encabezados = {
        Accept: "application/json",
        ...(opciones.body ? { "Content-Type": "application/json" } : {}),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...opciones.headers
    };

    let respuesta;
    try {
        respuesta = await fetch(`${API_BASE_URL}${ruta}`, {
            ...opciones,
            headers: encabezados
        });
    } catch (error) {
        const errorAPI = new Error("No se pudo establecer conexión con Laravel.");
        errorAPI.status = 0;
        throw errorAPI;
    }

    const texto = await respuesta.text();
    let datos = {};
    if (texto) {
        try {
            datos = JSON.parse(texto);
        } catch (error) {
            console.error("Laravel devolvió una respuesta no JSON:", error);
            throw new Error("La API respondió con un formato inesperado.");
        }
    }

    if (!respuesta.ok) {
        const error = new Error(datos.message || `Error HTTP ${respuesta.status}`);
        error.status = respuesta.status;
        error.data = datos;

        if (respuesta.status === 401 && ruta !== "/auth/login") {
            limpiarSesion();
        }

        throw error;
    }

    return datos;
}

function limpiarSesion() {
    sessionStorage.removeItem(TOKEN_KEY);
    usuarioActual = null;
    elementos.logoutLink.classList.add("hidden");
}

async function restaurarSesion() {
    if (!sessionStorage.getItem(TOKEN_KEY)) {
        return;
    }

    try {
        const respuesta = await apiRequest("/auth/me");
        usuarioActual = respuesta.usuario;
        elementos.logoutLink.classList.remove("hidden");
    } catch (error) {
        if (error.status !== 401) {
            console.error("No fue posible verificar la sesión:", error);
        }
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
    elementos.loginMessage.textContent = "";
    elementos.loginModal.classList.remove("hidden");
    document.getElementById("email").focus();
}

function cerrarLogin() {
    elementos.loginModal.classList.add("hidden");
}

async function login() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const boton = document.querySelector("#loginForm button[type='submit']");

    if (!email || !password) {
        elementos.loginMessage.textContent = "Completa el correo y la contraseña.";
        return;
    }

    elementos.loginMessage.textContent = "Conectando con Suplementor...";
    boton.disabled = true;

    try {
        const resultado = await apiRequest("/auth/login", {
            method: "POST",
            body: JSON.stringify({ correo: email, password })
        });

        sessionStorage.setItem(TOKEN_KEY, resultado.token);
        usuarioActual = resultado.usuario;
        elementos.logoutLink.classList.remove("hidden");
        elementos.loginMessage.textContent = "";
        cerrarLogin();
        alert(`Bienvenido, ${usuarioActual.nombre}.`);
    } catch (error) {
        console.error("No fue posible iniciar sesión:", error);
        elementos.loginMessage.textContent = mensajeError(error);
    } finally {
        boton.disabled = false;
    }
}

async function cerrarSesion() {
    try {
        await apiRequest("/auth/logout", { method: "POST" });
    } catch (error) {
        console.error("No fue posible cerrar la sesión en Laravel:", error);
    } finally {
        limpiarSesion();
        carrito = [];
        guardarCarrito();
        alert("Sesión cerrada.");
    }
}

function exigirSesionParaCompra() {
    if (usuarioActual) {
        return true;
    }

    abrirLogin();
    elementos.loginMessage.textContent = "Inicia sesión para agregar productos al carrito.";
    return false;
}

function esAdministrador() {
    return usuarioActual && usuarioActual.rol === "Administrador";
}

async function mostrarProductos() {
    abrirModal("Catálogo de productos", "<p>Cargando productos...</p>");

    try {
        const respuesta = await apiRequest("/productos");
        const lista = respuesta.datos || [];
        const formulario = esAdministrador() ? `
            <form id="productCreateForm" class="api-form">
                <h3>Agregar producto</h3>
                <label>Nombre<input name="nombre" maxlength="120" required></label>
                <label>Descripción<input name="descripcion" maxlength="255" required></label>
                <label>Precio<input name="precio" type="number" min="0" step="0.01" required></label>
                <label>Stock inicial<input name="stock" type="number" min="0" step="1" required></label>
                <button type="submit">Guardar producto</button>
                <p class="api-message" role="status"></p>
            </form>` : "";

        const tarjetas = lista.length ? lista.map((producto, indice) => `
            <article class="card">
                <i class="fa-solid ${iconosProducto[indice % iconosProducto.length]}" aria-hidden="true"></i>
                <h3>${escaparHTML(producto.nombre)}</h3>
                <p>${escaparHTML(producto.descripcion)}</p>
                <p>${formatearPrecio(Number(producto.precio))} · Stock: ${producto.stock}</p>
                ${esAdministrador() ? `
                    <form class="product-edit-form api-form" data-product-id="${producto.id}">
                        <label>Nombre<input name="nombre" maxlength="120" value="${escaparHTML(producto.nombre)}" required></label>
                        <label>Descripción<input name="descripcion" maxlength="255" value="${escaparHTML(producto.descripcion)}" required></label>
                        <label>Precio<input name="precio" type="number" min="0" step="0.01" value="${producto.precio}" required></label>
                        <label>Stock<input name="stock" type="number" min="0" step="1" value="${producto.stock}" required></label>
                        <button type="submit">Actualizar</button>
                        <button type="button" class="btn-close" onclick="eliminarProducto(${producto.id})">Eliminar</button>
                    </form>` : ""}
                <button type="button" onclick="agregarCarrito(${producto.id})">Agregar al carrito</button>
            </article>
        `).join("") : "<p>Aún no hay productos registrados.</p>";

        elementos.contenidoModal.innerHTML = `${formulario}<div class="cards-grid">${tarjetas}</div>`;
    } catch (error) {
        console.error("No fue posible cargar el catálogo:", error);
        elementos.contenidoModal.innerHTML = `<p class="api-message">${escaparHTML(mensajeError(error))}</p>`;
    }
}

async function agregarCarrito(productoId) {
    if (!exigirSesionParaCompra()) {
        return;
    }

    try {
        const respuesta = await apiRequest(`/productos/${productoId}`);
        const producto = respuesta.producto;
        if (producto.stock < 1) {
            alert("Este producto no tiene unidades disponibles.");
            return;
        }

        const enCarrito = carrito.find((item) => item.producto_id === producto.id);
        if (enCarrito) {
            if (enCarrito.cantidad >= producto.stock) {
                alert("No hay suficiente stock para agregar otra unidad.");
                return;
            }
            enCarrito.cantidad += 1;
        } else {
            carrito.push({
                producto_id: producto.id,
                nombre: producto.nombre,
                precio: Number(producto.precio),
                cantidad: 1
            });
        }

        guardarCarrito();
        alert(`${producto.nombre} agregado al carrito.`);
    } catch (error) {
        alert(mensajeError(error));
    }
}

async function eliminarProducto(productoId) {
    if (!confirm("¿Deseas eliminar este producto del catálogo?")) {
        return;
    }

    try {
        await apiRequest(`/productos/${productoId}`, { method: "DELETE" });
        carrito = carrito.filter((item) => item.producto_id !== productoId);
        guardarCarrito();
        await mostrarProductos();
    } catch (error) {
        alert(mensajeError(error));
    }
}

async function mostrarInventario() {
    abrirModal("Inventario", "<p>Cargando inventario...</p>");

    try {
        const respuesta = await apiRequest("/productos");
        const tarjetas = (respuesta.datos || []).map((producto) => `
            <article class="card">
                <i class="fa-solid fa-warehouse" aria-hidden="true"></i>
                <h3>${escaparHTML(producto.nombre)}</h3>
                <p>Unidades disponibles: <strong>${producto.stock}</strong></p>
                ${esAdministrador() ? `
                    <form class="inventory-form api-form" data-product-id="${producto.id}">
                        <label>Actualizar stock<input name="stock" type="number" min="0" step="1" value="${producto.stock}" required></label>
                        <button type="submit">Guardar stock</button>
                        <p class="api-message" role="status"></p>
                    </form>` : ""}
            </article>
        `).join("");

        elementos.contenidoModal.innerHTML = tarjetas || "<p>No hay productos para mostrar.</p>";
    } catch (error) {
        elementos.contenidoModal.innerHTML = `<p class="api-message">${escaparHTML(mensajeError(error))}</p>`;
    }
}

function mostrarVentas() {
    if (!exigirSesionParaCompra()) {
        return;
    }

    if (!carrito.length) {
        abrirModal("Carrito de compras", "<h3>El carrito está vacío.</h3><p>Agrega productos del catálogo para iniciar tu compra.</p>");
        return;
    }

    const tarjetas = carrito.map((producto) => `
        <article class="card">
            <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
            <h3>${escaparHTML(producto.nombre)}</h3>
            <p>${formatearPrecio(producto.precio)} × ${producto.cantidad}</p>
            <button type="button" onclick="cambiarCantidad(${producto.producto_id}, -1)">−</button>
            <button type="button" onclick="cambiarCantidad(${producto.producto_id}, 1)">+</button>
            <button type="button" class="btn-close" onclick="quitarDelCarrito(${producto.producto_id})">Quitar</button>
        </article>
    `).join("");
    const total = carrito.reduce((suma, producto) => suma + producto.precio * producto.cantidad, 0);

    abrirModal("Carrito de compras", `
        <div class="cards-grid">${tarjetas}</div>
        <p class="carrito-total">Total estimado: ${formatearPrecio(total)}</p>
        <p>El total definitivo se calcula en el servidor usando los precios y el inventario actuales.</p>
        <p id="saleMessage" class="api-message" role="status"></p>
        <button type="button" onclick="comprar()">Confirmar compra</button>
    `);
}

function cambiarCantidad(productoId, cambio) {
    const producto = carrito.find((item) => item.producto_id === productoId);
    if (!producto) {
        return;
    }

    producto.cantidad += cambio;
    if (producto.cantidad <= 0) {
        carrito = carrito.filter((item) => item.producto_id !== productoId);
    }

    guardarCarrito();
    mostrarVentas();
}

function quitarDelCarrito(productoId) {
    carrito = carrito.filter((item) => item.producto_id !== productoId);
    guardarCarrito();
    mostrarVentas();
}

async function comprar() {
    const mensaje = document.getElementById("saleMessage");

    try {
        const resultado = await apiRequest("/ventas", {
            method: "POST",
            body: JSON.stringify({
                productos: carrito.map(({ producto_id, cantidad }) => ({
                    producto_id,
                    cantidad
                }))
            })
        });

        carrito = [];
        guardarCarrito();
        cerrarModal();
        elementos.compraModal.classList.remove("hidden");
        elementos.compraModal.querySelector("p").innerHTML =
            `Venta #${resultado.venta.id} registrada correctamente.<br><br>Total pagado: <strong>${formatearPrecio(Number(resultado.venta.total))}</strong>`;
    } catch (error) {
        console.error("No fue posible registrar la venta:", error);
        if (mensaje) {
            mensaje.textContent = mensajeError(error);
        } else {
            alert(mensajeError(error));
        }
    }
}

function cerrarCompra() {
    elementos.compraModal.classList.add("hidden");
}

function formatearPrecio(valor) {
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        maximumFractionDigits: 0
    }).format(valor);
}

async function mostrarUsuarios() {
    elementos.usuariosModal.classList.remove("hidden");
    const lista = elementos.usuariosModal.querySelector(".lista");

    if (!esAdministrador()) {
        lista.innerHTML = "<p>La gestión de usuarios está disponible solo para administradores.</p>";
        return;
    }

    lista.innerHTML = "<p>Cargando usuarios...</p>";
    try {
        const respuesta = await apiRequest("/usuarios");
        const usuarios = respuesta.datos || [];
        const filas = usuarios.map((usuario) => `
            <div class="item">
                <i class="fa-solid fa-user"></i>
                ${escaparHTML(usuario.nombre)} · ${escaparHTML(usuario.correo)} · ${escaparHTML(usuario.rol)}
            </div>
        `).join("");

        lista.innerHTML = `
            <form id="userCreateForm" class="api-form">
                <h3>Crear usuario</h3>
                <label>Nombre<input name="nombre" maxlength="100" required></label>
                <label>Correo<input name="correo" type="email" maxlength="150" required></label>
                <label>Contraseña<input name="password" type="password" minlength="8" required></label>
                <label>Rol
                    <select name="rol" required>
                        <option>Cliente</option>
                        <option>Empleado</option>
                        <option>Supervisor</option>
                        <option>Administrador</option>
                    </select>
                </label>
                <button type="submit">Crear usuario</button>
                <p class="api-message" role="status"></p>
            </form>
            <div class="lista">${filas || "<p>No hay usuarios.</p>"}</div>
        `;
    } catch (error) {
        lista.innerHTML = `<p class="api-message">${escaparHTML(mensajeError(error))}</p>`;
    }
}

function cerrarUsuarios() {
    elementos.usuariosModal.classList.add("hidden");
}

async function mostrarReportes() {
    elementos.reportesModal.classList.remove("hidden");
    const lista = elementos.reportesModal.querySelector(".lista");

    if (!esAdministrador()) {
        lista.innerHTML = "<p>Los reportes de ventas están disponibles solo para administradores.</p>";
        return;
    }

    lista.innerHTML = "<p>Cargando ventas...</p>";
    try {
        const respuesta = await apiRequest("/ventas");
        const ventas = respuesta.datos || [];
        const total = ventas.reduce((suma, venta) => suma + Number(venta.total), 0);
        const filas = ventas.map((venta) => `
            <div class="item">
                <i class="fa-solid fa-receipt"></i>
                Venta #${venta.id} · ${escaparHTML(venta.user.nombre)} · ${formatearPrecio(Number(venta.total))} · ${escaparHTML(venta.creado_en)}
            </div>
        `).join("");

        lista.innerHTML = `
            <p>Ventas registradas: <strong>${ventas.length}</strong></p>
            <p>Total acumulado: <strong>${formatearPrecio(total)}</strong></p>
            ${filas || "<p>Aún no hay ventas registradas.</p>"}
        `;
    } catch (error) {
        lista.innerHTML = `<p class="api-message">${escaparHTML(mensajeError(error))}</p>`;
    }
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

async function manejarFormularioAPI(evento) {
    const formulario = evento.target;
    if (!(formulario instanceof HTMLFormElement)) {
        return;
    }
    if (formulario.id === "loginForm") {
        return;
    }

    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(formulario).entries());
    const mensaje = formulario.querySelector(".api-message");
    const boton = formulario.querySelector("button[type='submit']");
    if (boton) {
        boton.disabled = true;
    }
    if (mensaje) {
        mensaje.textContent = "Guardando...";
    }

    try {
        if (formulario.id === "productCreateForm") {
            await apiRequest("/productos", {
                method: "POST",
                body: JSON.stringify({
                    ...datos,
                    precio: Number(datos.precio),
                    stock: Number(datos.stock)
                })
            });
            await mostrarProductos();
        } else if (formulario.classList.contains("product-edit-form")) {
            await apiRequest(`/productos/${formulario.dataset.productId}`, {
                method: "PUT",
                body: JSON.stringify({
                    ...datos,
                    precio: Number(datos.precio),
                    stock: Number(datos.stock)
                })
            });
            await mostrarProductos();
        } else if (formulario.classList.contains("inventory-form")) {
            await apiRequest(`/inventario/${formulario.dataset.productId}`, {
                method: "PUT",
                body: JSON.stringify({ stock: Number(datos.stock) })
            });
            await mostrarInventario();
        } else if (formulario.id === "userCreateForm") {
            await apiRequest("/usuarios", {
                method: "POST",
                body: JSON.stringify(datos)
            });
            await mostrarUsuarios();
        }
    } catch (error) {
        console.error("No fue posible guardar los datos:", error);
        if (mensaje) {
            mensaje.textContent = mensajeError(error);
        } else {
            alert(mensajeError(error));
        }
    } finally {
        if (boton && boton.isConnected) {
            boton.disabled = false;
        }
    }
}

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

    const modales = [
        elementos.modal,
        elementos.loginModal,
        elementos.compraModal,
        elementos.usuariosModal,
        elementos.reportesModal,
        elementos.inicioModal
    ];
    if (modales.includes(evento.target)) {
        evento.target.classList.add("hidden");
    }
});

document.addEventListener("submit", manejarFormularioAPI);
document.getElementById("loginForm").addEventListener("submit", (evento) => {
    evento.preventDefault();
    login();
});

actualizarContadorCarrito();
restaurarSesion();
