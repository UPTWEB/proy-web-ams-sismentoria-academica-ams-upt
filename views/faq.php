<?php
$pageTitle = "Preguntas Frecuentes";
require_once BASE_PATH . '/views/components/head.php';
require_once BASE_PATH . '/views/components/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4"><i class="fas fa-question-circle me-2"></i> Preguntas Frecuentes (FAQ)</h1>

    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq1-heading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                    ¿Cómo me registro como estudiante?
                </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Puedes registrarte desde la opción "Iniciar Sesión" y luego seleccionar "Registrarse". Asegúrate de tener tu código universitario a mano.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq2-heading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    ¿Qué hago si olvidé mi contraseña?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    En la pantalla de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?" y sigue los pasos para recuperarla.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq3-heading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    ¿Cómo solicito una clase de mentoría?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Inicia sesión como estudiante y usa la opción "Solicitar Clase". Selecciona el área de apoyo que necesitas.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/components/footer.php'; ?>
