<?php
// config/mongodb.php
require_once BASE_PATH . '/vendor/autoload.php';

class Mongodb {
    
    public $client;
    public $database;
    public $collection;
    private $mongoUri;
    
    private const DATABASE_NAME = 'test'; 
    private const COLLECTION_NAME = 'accions';
    
    public function __construct($mongoUri = null) {
        try {
            $this->mongoUri = $mongoUri ?? 'mongodb+srv://gh2022073898:3qh1hQb37GGB4HYY@cluster0.wfxnce1.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
            
            if (!$this->mongoUri) {
                throw new Exception("MongoDB URI no configurada");
            }

            $this->client = new MongoDB\Client($this->mongoUri);
            $this->database = $this->client->selectDatabase(self::DATABASE_NAME);
            $this->collection = $this->database->selectCollection(self::COLLECTION_NAME);
            
            // Verificar conexión
            $this->client->selectDatabase('admin')->command(['ping' => 1]);
            error_log("✅ MongoDB conectado a base de datos: " . self::DATABASE_NAME);
            
        } catch (Exception $e) {
            error_log("❌ Error conectando MongoDB: " . $e->getMessage());
            throw new Exception("No se pudo conectar a MongoDB: " . $e->getMessage());
        }
    }
    
    public function verificarConexion() {
        try {
            $this->client->selectDatabase('admin')->command(['ping' => 1]);
            return true;
        } catch (Exception $e) {
            error_log("❌ Conexión MongoDB perdida: " . $e->getMessage());
            return false;
        }
    }
    
    public function cerrarConexion() {
        $this->client = null;
        $this->database = null;
        $this->collection = null;
    }
    
    public function __destruct() {
        $this->cerrarConexion();
    }
}
?>