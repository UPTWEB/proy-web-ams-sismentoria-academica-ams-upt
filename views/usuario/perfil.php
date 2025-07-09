<?php
require_once BASE_PATH . '/models/Usuario.php';

$usuarioModel = new Usuario();

$usuario = $usuarioModel->obtenerDatosCompletos($_SESSION['usuario_id']);
$roles = $usuarioModel->obtenerRolesUsuario($_SESSION['usuario_id']);

function validarPasswordSegura($password) {
    if (strlen($password) < 8) {
        return "La contraseña debe tener al menos 8 caracteres";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "La contraseña debe contener al menos una letra mayúscula";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "La contraseña debe contener al menos una letra minúscula";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "La contraseña debe contener al menos un número";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return "La contraseña debe contener al menos un carácter especial";
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        if (empty($password)) {
            throw new Exception("La contraseña es requerida");
        }
        
        if ($password !== $confirm_password) {
            throw new Exception("Las contraseñas no coinciden");
        }

        $validacion = validarPasswordSegura($password);
        if ($validacion !== true) {
            throw new Exception($validacion);
        }

        $usuarioModel->actualizarPassword($_SESSION['usuario_id'], $password);
        
        $mensaje = "Contraseña actualizada correctamente";
        $tipo_mensaje = "success";
    } catch (Exception $e) {
        $mensaje = "Error al actualizar contraseña: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

$login_time = $_SESSION['login_time'] ?? time();
$tiempo_conectado = time() - $login_time;

function formatearTiempo($segundos) {
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    $segundos = $segundos % 60;
    
    if ($horas > 0) {
        return sprintf("%d hora(s), %d minuto(s)", $horas, $minutos);
    } elseif ($minutos > 0) {
        return sprintf("%d minuto(s), %d segundo(s)", $minutos, $segundos);
    } else {
        return sprintf("%d segundo(s)", $segundos);
    }
}

$tiempo_conectado_texto = formatearTiempo($tiempo_conectado);

$roles_nombres = array_column($roles, 'NOMBRE');
$rol_principal = !empty($roles_nombres) ? $roles_nombres[0] : 'Sin rol asignado';

require_once BASE_PATH . '/views/components/head.php';
require_once BASE_PATH . '/views/components/header.php';
?>

<style>
:root {
    --accent-blue: #5a73c4;
    --light-blue: #e8f0fe;
    --dark-blue: #2d4482;
    --success-green: #28a745;
    --warning-orange: #ffc107;
    --danger-red: #dc3545;
    --light-gray: #f8f9fa;
    --border-gray: #e9ecef;
    --text-gray: #495057;
}

.profile-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(60, 90, 166, 0.1);
    transition: all 0.15s ease-in-out;
    border-radius: 0.5rem;
}

.profile-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(60, 90, 166, 0.15);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    color: white;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.card-header h3, .card-header h5 {
    margin: 0;
    font-weight: 600;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.form-label.fw-bold {
    color: var(--text-gray);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.form-control[readonly] {
    background-color: var(--light-gray);
    border-color: var(--border-gray);
    color: var(--text-gray);
}

.form-control:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 0.2rem rgba(60, 90, 166, 0.25);
}

.badge-status {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
}

.bg-primary {
    background-color: var(--primary-blue) !important;
}

.bg-secondary {
    background-color: var(--secondary-blue) !important;
}

.bg-info {
    background-color: var(--accent-blue) !important;
}

.btn-primary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all 0.15s ease-in-out;
}

.btn-primary:hover {
    background-color: var(--dark-blue);
    border-color: var(--dark-blue);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(60, 90, 166, 0.3);
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    transition: all 0.15s ease-in-out;
}

.btn-secondary:hover {
    transform: translateY(-1px);
}

.btn-outline-secondary {
    color: var(--primary-blue);
    border-color: var(--primary-blue);
}

.btn-outline-secondary:hover {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
}

.info-row {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-gray);
    transition: background-color 0.15s ease;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row:hover {
    background-color: rgba(60, 90, 166, 0.02);
    border-radius: 0.25rem;
    margin: 0 -0.5rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-indicator {
    height: 6px;
    border-radius: 3px;
    background-color: var(--border-gray);
    overflow: hidden;
    margin-bottom: 0.5rem;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.strength-bar {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-weak { 
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
    width: 25%; 
}

.strength-fair { 
    background: linear-gradient(135deg, #fd7e14 0%, #e55100 100%); 
    width: 50%; 
}

.strength-good { 
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); 
    width: 75%; 
}

.strength-strong { 
    background: linear-gradient(135deg, var(--success-green) 0%, #1e7e34 100%); 
    width: 100%; 
}

.alert-info {
    background-color: var(--light-blue);
    border-color: var(--primary-blue);
    color: var(--dark-blue);
}

.alert-warning {
    background-color: #fff3cd;
    border-color: var(--warning-orange);
    color: #856404;
}

.text-success {
    color: var(--success-green) !important;
}

.is-valid {
    border-color: var(--success-green);
}

.is-invalid {
    border-color: var(--danger-red);
}

.input-group .btn {
    border-color: var(--border-gray);
}

.card-body {
    padding: 1.5rem;
}

/* Animaciones suaves */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-card {
    animation: fadeIn 0.3s ease-out;
}

.profile-card:nth-child(2) { animation-delay: 0.1s; }
.profile-card:nth-child(3) { animation-delay: 0.2s; }
.profile-card:nth-child(4) { animation-delay: 0.3s; }
.profile-card:nth-child(5) { animation-delay: 0.4s; }

/* Responsive improvements */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .profile-card {
        margin-bottom: 1rem;
    }
    
    .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card profile-card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-user me-2"></i> Información Personal
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre:</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['NOMBRE'] ?? 'No especificado') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Apellido:</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['APELLIDO'] ?? 'No especificado') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">DNI:</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['DNI'] ?? 'No asignado') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Celular:</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['CELULAR'] ?? 'No registrado') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico:</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['EMAIL'] ?? '') ?>" readonly>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> 
                        Los datos personales no pueden ser modificados. Contacta al administrador si necesitas realizar cambios.
                    </div>
                </div>
            </div>

            <div class="card profile-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i> Cambiar Contraseña
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>/public/index.php?accion=perfil" id="passwordForm">
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Ingresa tu nueva contraseña" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-indicator">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                                <small class="text-muted" id="strengthText">Ingresa una contraseña para ver su fortaleza</small>
                            </div>
                            <div class="form-text mt-2">
                                <strong>Requisitos de seguridad:</strong>
                                <ul class="mb-0 mt-1" id="requirements">
                                    <li id="req-length" class="text-muted">
                                        <i class="fas fa-times me-1"></i> Mínimo 8 caracteres
                                    </li>
                                    <li id="req-upper" class="text-muted">
                                        <i class="fas fa-times me-1"></i> Al menos una letra mayúscula
                                    </li>
                                    <li id="req-lower" class="text-muted">
                                        <i class="fas fa-times me-1"></i> Al menos una letra minúscula
                                    </li>
                                    <li id="req-number" class="text-muted">
                                        <i class="fas fa-times me-1"></i> Al menos un número
                                    </li>
                                    <li id="req-special" class="text-muted">
                                        <i class="fas fa-times me-1"></i> Al menos un carácter especial (!@#$%^&*)
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label fw-bold">Confirmar Nueva Contraseña:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirma tu nueva contraseña" required>
                                <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="confirmFeedback"></div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i> Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>

            <div class="card profile-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Información de la Cuenta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <strong>ID de Usuario:</strong> 
                                <span class="badge bg-secondary ms-2"><?= $_SESSION['usuario_id'] ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <strong>Roles Asignados:</strong> 
                                <div class="mt-1">
                                    <?php foreach ($roles as $rol): ?>
                                        <span class="badge bg-primary me-1"><?= htmlspecialchars($rol['NOMBRE']) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (empty($roles)): ?>
                                        <span class="badge bg-warning">Sin roles asignados</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <strong>Fecha de Registro:</strong> 
                                <?= $usuario['FECHA_REG'] ? date('d/m/Y H:i:s', strtotime($usuario['FECHA_REG'])) : 'No disponible' ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <strong>Tiempo Conectado:</strong> 
                                <span class="badge bg-success" id="tiempoConectado"><?= $tiempo_conectado_texto ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($usuario['ID_ESTUDIANTE'])): ?>
                <div class="card profile-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i> Información Académica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email Corporativo:</label>
                                    <input type="email" class="form-control" 
                                           value="<?= htmlspecialchars($usuario['EMAIL_CORPORATIVO'] ?? 'No asignado') ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Código de Estudiante:</label>
                                    <input type="text" class="form-control" 
                                           value="<?= htmlspecialchars($usuario['CODIGO'] ?? 'No asignado') ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Condición Académica:</label>
                                    <div class="mt-2">
                                        <?php
                                        $condicion = $usuario['CONDICION'] ?? 'No definida';
                                        $clase_badge = match(strtolower($condicion)) {
                                            'activo' => 'bg-success',
                                            'inactivo' => 'bg-danger',
                                            'suspendido' => 'bg-warning',
                                            'egresado' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $clase_badge ?> badge-status">
                                            <?= htmlspecialchars($condicion) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Estudiante:</label>
                                    <input type="text" class="form-control" 
                                           value="<?= htmlspecialchars($usuario['ID_ESTUDIANTE']) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            La información académica es administrada por el sistema y no puede ser modificada por el estudiante.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($usuario['ID_DOCENTE'])): ?>
                <div class="card profile-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Información de Docente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <strong>ID Docente:</strong> 
                                    <span class="badge bg-info"><?= $usuario['ID_DOCENTE'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <strong>Estado:</strong> 
                                    <span class="badge <?= $usuario['ESTADO'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $usuario['ESTADO'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($usuario['ID_ADMIN'])): ?>
                <div class="card profile-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i> Información de Administrador
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <strong>ID Administrador:</strong> 
                                    <span class="badge bg-danger"><?= $usuario['ID_ADMIN'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <strong>Nivel de Acceso:</strong> 
                                    <span class="badge bg-warning">Nivel <?= $usuario['NIVEL_ACCESO'] ?? 'No definido' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/public/index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Volver al Inicio
                </a>
                <?php if (!empty($usuario['ID_ESTUDIANTE'])): ?>
                <a href="<?= BASE_URL ?>/public/index.php?accion=estudiante" class="btn btn-primary">
                    <i class="fas fa-graduation-cap me-2"></i> Panel Estudiante
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const submitBtn = document.getElementById('submitBtn');
    const confirmFeedback = document.getElementById('confirmFeedback');

    const loginTime = <?= $login_time ?>;
    const tiempoConectadoElement = document.getElementById('tiempoConectado');
    
    function actualizarTiempo() {
        const tiempoActual = Math.floor(Date.now() / 1000);
        const segundosConectado = tiempoActual - loginTime;
        
        const horas = Math.floor(segundosConectado / 3600);
        const minutos = Math.floor((segundosConectado % 3600) / 60);
        const segundos = segundosConectado % 60;
        
        let texto = '';
        if (horas > 0) {
            texto = `${horas} hora(s), ${minutos} minuto(s)`;
        } else if (minutos > 0) {
            texto = `${minutos} minuto(s), ${segundos} segundo(s)`;
        } else {
            texto = `${segundos} segundo(s)`;
        }
        
        tiempoConectadoElement.textContent = texto;
    }
    
    setInterval(actualizarTiempo, 1000);

    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        checkPasswordStrength(password);
        validateForm();
    });

    confirmPasswordInput.addEventListener('input', function() {
        validatePasswordMatch();
        validateForm();
    });

    function checkPasswordStrength(password) {
        const requirements = [
            { id: 'req-length', regex: /.{8,}/, text: 'Mínimo 8 caracteres' },
            { id: 'req-upper', regex: /[A-Z]/, text: 'Al menos una letra mayúscula' },
            { id: 'req-lower', regex: /[a-z]/, text: 'Al menos una letra minúscula' },
            { id: 'req-number', regex: /[0-9]/, text: 'Al menos un número' },
            { id: 'req-special', regex: /[^A-Za-z0-9]/, text: 'Al menos un carácter especial' }
        ];

        let score = 0;
        requirements.forEach(req => {
            const element = document.getElementById(req.id);
            const icon = element.querySelector('i');
            
            if (req.regex.test(password)) {
                score++;
                element.classList.remove('text-muted');
                element.classList.add('text-success');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        });

        strengthBar.className = 'strength-bar';
        if (password.length === 0) {
            strengthText.textContent = 'Ingresa una contraseña para ver su fortaleza';
            strengthBar.style.width = '0%';
        } else if (score < 2) {
            strengthBar.classList.add('strength-weak');
            strengthText.textContent = 'Contraseña muy débil';
        } else if (score < 3) {
            strengthBar.classList.add('strength-fair');
            strengthText.textContent = 'Contraseña débil';
        } else if (score < 5) {
            strengthBar.classList.add('strength-good');
            strengthText.textContent = 'Contraseña buena';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.textContent = 'Contraseña fuerte';
        }

        return score === 5;
    }

    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword === '') {
            confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
            confirmFeedback.textContent = '';
            return false;
        }

        if (password === confirmPassword) {
            confirmPasswordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.add('is-valid');
            confirmFeedback.textContent = '';
            return true;
        } else {
            confirmPasswordInput.classList.remove('is-valid');
            confirmPasswordInput.classList.add('is-invalid');
            confirmFeedback.textContent = 'Las contraseñas no coinciden';
            return false;
        }
    }

    function validateForm() {
        const isPasswordStrong = checkPasswordStrength(passwordInput.value);
        const isPasswordMatch = validatePasswordMatch();
        const hasPassword = passwordInput.value.length > 0;
        const hasConfirmPassword = confirmPasswordInput.value.length > 0;

        submitBtn.disabled = !(isPasswordStrong && isPasswordMatch && hasPassword && hasConfirmPassword);
    }
});
</script>

<?php require_once BASE_PATH . '/views/components/footer.php'; ?>