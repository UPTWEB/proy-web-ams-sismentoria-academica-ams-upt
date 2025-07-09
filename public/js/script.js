function confirmarCerrarSesion(e, logoutUrl) {
    e.preventDefault();

    if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = "100vw";
        overlay.style.height = "100vh";
        overlay.style.backgroundColor = "rgba(0,0,0,0.6)";
        overlay.style.display = "flex";
        overlay.style.justifyContent = "center";
        overlay.style.alignItems = "center";
        overlay.style.zIndex = 9999;

        const spinner = document.createElement("div");
        spinner.innerHTML = `
            <div style="color: white; text-align: center;">
                <div class="loader"></div>
                <p style="margin-top: 10px;">Cerrando sesión...</p>
            </div>
        `;

        overlay.appendChild(spinner);
        document.body.appendChild(overlay);

        setTimeout(() => {
            window.location.href = logoutUrl;
        }, 2000);
    }
}
let TOKEN_API_DNI = "";
let codigoEnviado = "";
const BASE_URL = window.location.origin + "/web_asm";

fetch("/web_asm/config.json")
    .then(response => {
        if (!response.ok) throw new Error("No se pudo cargar config.json");
        return response.json();
    })
    .then(config => {
        TOKEN_API_DNI = config.apitoken;
    })
    .catch(err => {
        console.error("Error cargando config.json:", err);
    });

const form = document.getElementById("formRegistro");
const btnRegistrar = document.getElementById("btnRegistrar");

if (form) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const correo = document.getElementById("email").value;
        if (!correo) {
            alert("El correo es obligatorio.");
            return;
        }

        mostrarCarga(true);

        const formData = new FormData();
        formData.append("email", correo);
        console.log("📤 Enviando solicitud de código...");

        fetch(`${BASE_URL}/auth/notify.php`, {
            method: "POST",
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`);
            return res.json();
        })
        .then(data => {
            if (data.success && data.codigo) {
                codigoEnviado = data.codigo;
                document.getElementById("modalCodigo").style.display = "block";
            } else if (!data.success) {
                alert("❌ " + (data.message || "No se pudo enviar el código."));
            } else {
                alert("❌ El servidor no devolvió el código.");
            }
        })
        .catch(err => {
            console.error("❌ Error al contactar al servidor:", err);
            alert("❌ No se pudo contactar con el servidor. Revisa la consola.");
        })
        .finally(() => mostrarCarga(false));
    });
}
function validarDNI(dni) {
    if (/^\d{8}$/.test(dni)) {
        mostrarCarga(true);
        fetch(`https://dniruc.apisperu.com/api/v1/dni/${dni}?token=${TOKEN_API_DNI}`)
            .then(res => res.json())
            .then(data => {
                if (data.nombres) {
                    document.getElementById("nombre").value = data.nombres;
                    document.getElementById("apellido").value = `${data.apellidoPaterno} ${data.apellidoMaterno}`;
                    document.getElementById("email").focus();
                } else {
                    limpiarDNI("DNI no encontrado.");
                }
            })
            .catch(() => limpiarDNI("Error al consultar el DNI."))
            .finally(() => mostrarCarga(false));
    } else {
        document.getElementById("nombre").value = "";
        document.getElementById("apellido").value = "";
    }
}

function limpiarDNI(msg) {
    alert(msg);
    document.getElementById("dni").value = "";
    document.getElementById("nombre").value = "";
    document.getElementById("apellido").value = "";
    document.getElementById("dni").focus();
}

function validarCorreo(input) {
    const correo = input.value;
    const regex = /^[\w\-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
    document.getElementById("correoError").textContent = regex.test(correo) ? "" : "Correo no válido";
}

function validarPassword(pass) {
    const fuerte = /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%\^&\*])/;
    document.getElementById("passError").textContent = fuerte.test(pass)
        ? ""
        : "Contraseña insegura. Usa mayúsculas, números y símbolos.";
    compararPassword();
}

function compararPassword() {
    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirmar").value;
    const valido = pass && confirm && pass === confirm;
    document.getElementById("passError").textContent = !valido ? "Las contraseñas no coinciden." : "";
    document.getElementById("btnRegistrar").disabled = !valido;
}

function mostrarCarga(estado) {
    if (estado) {
        const carga = document.createElement("div");
        carga.id = "cargandoOverlay";
        carga.style = "position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;";
        carga.innerHTML = '<div style="color:white;">Validando...<div class="loader"></div></div>';
        document.body.appendChild(carga);
    } else {
        const carga = document.getElementById("cargandoOverlay");
        if (carga) carga.remove();
    }
}

function verificarCodigo() {
    const input = document.getElementById("codigoVerificacion").value;

    if (input !== codigoEnviado) {
        alert("⚠️ Código incorrecto.");
        return;
    }

    const form = document.getElementById("formRegistro");
    const formData = new FormData(form);

    console.log("📤 Enviando datos del formulario:");
    for (let pair of formData.entries()) {
        console.log(`🔹 ${pair[0]}: ${pair[1]}`);
    }

    mostrarCarga(true);
    fetch(`${BASE_URL}/public/index.php?accion=procesar_registro`, {
        method: "POST",
        body: formData
    })
    .then(async res => {
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch (err) {
            throw new Error("Respuesta inválida del servidor: " + text);
        }
    })
    .then(data => {
        if (data.success) {
            alert("✅ Registro completado.");
            window.location.href = `${BASE_URL}/public/index.php`;
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch((err) => {
        console.error("❌ Error al registrar:", err);
        alert("❌ Error al registrar: " + err.message);
    })
    .finally(() => mostrarCarga(false));
}

document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.header');
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    const currentUrl = window.location.href;
    navLinks.forEach(link => {
        if (currentUrl.includes(link.href) && link.href !== window.location.origin + '/') {
            link.setAttribute('data-active', 'true');
        }
    });
    
    // Cerrar menú móvil al hacer clic en un enlace
    const mobileLinks = document.querySelectorAll('.mobile-nav-link');
    const mobileMenu = document.getElementById('mobileMenu');
    const toggleButton = document.querySelector('.navbar-toggler');
    
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.remove('show');
            toggleButton.setAttribute('aria-expanded', 'false');
        });
    });
    
    // Efecto parallax sutil en el header
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        header.style.transform = `translateY(${rate}px)`;
    });
});

