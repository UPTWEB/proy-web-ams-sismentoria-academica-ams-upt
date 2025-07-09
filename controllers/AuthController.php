<?php
require_once BASE_PATH . '/models/Usuario.php';

class AuthController extends BaseController {

    public function handle($accion) {
        switch ($accion) {
            case 'login':
                $this->loginGet();
                break;
            case 'procesar_login':
                $this->loginPost();
                break;
            case 'registro':
                $this->registroGet();
                break;
            case 'procesar_registro':
                $this->registroPost();
                break;
            default:
                echo "<h2>Acción de autenticación no válida: " . htmlspecialchars($accion) . "</h2>";
                break;
        }
    }

    public function loginGet() {
        require BASE_PATH . '/views/login.php';
    }

    public function loginPost() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $usuario = new Usuario();
        $datos = $usuario->verificarCredenciales($email, $password);

        if ($datos) {
            session_start();
            $_SESSION['usuario_id'] = $datos['ID_USUARIO'];
            $_SESSION['rol_id'] = (int)$datos['ID_ROL']; 
            $_SESSION['rol_nombre'] = $datos['ROL'];   

            header('Location: ' . BASE_URL . '/public/index.php');
        } else {
            echo "<script>alert('Credenciales incorrectas');window.location.href='" . BASE_URL . "/public/index.php?accion=login';</script>";
        }
    }

    public function registroGet() {
        require BASE_PATH . '/views/register.php';
    }
    public function registroPost() {
        header('Content-Type: application/json');

        $dni      = $_POST['dni'] ?? '';
        $nombre   = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $usuarioModel = new Usuario();
            $user_id = $usuarioModel->registrarUsuario($dni, $nombre, $apellido, $email, $password);

            if ($user_id) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['usuario_id'] = $user_id;

                $datosUsuario = $usuarioModel->buscarPorCorreo($email);
                if ($datosUsuario) {
                    $_SESSION['rol_id'] = (int)$datosUsuario['ID_ROL'];
                    $_SESSION['rol_nombre'] = $datosUsuario['ROL'];
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Registro exitoso'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar el usuario'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el registro'
            ]);
        }

        exit;
    }
}
