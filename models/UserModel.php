<?php
// models/UserModel.php
class UserModel {
    private $conn;
    private $table_name = "usuarios";
    private $table_tokens = "tokens_recuperacion";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByDocument($documento) {
        $query = "SELECT id, documento, password, rol FROM " . $this->table_name . " WHERE documento = :documento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function createUser($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (documento, password, rol) 
                  VALUES (:documento, :password, :rol)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento', $data['documento']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':rol', $data['rol']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT id, documento, rol FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table_name . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function userExists($documento) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE documento = :documento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // ================== MÉTODOS PARA TOKENS ==================

    // Guardar token genérico
    public function guardarTokenGenerico($token, $expiracion, $correo) {
        $query = "INSERT INTO " . $this->table_tokens . " (token, correo, expiracion) 
                  VALUES (:token, :correo, :expiracion)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':expiracion', $expiracion);

        return $stmt->execute();
    }

    // Validar token genérico
    public function validarTokenGenerico($token) {
        $query = "SELECT id FROM " . $this->table_tokens . " 
                  WHERE token = :token AND expiracion > NOW() AND usado = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Limpiar token después de usarlo
    public function limpiarToken($token) {
        $query = "UPDATE " . $this->table_tokens . " SET usado = 1 WHERE token = :token";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);

        return $stmt->execute();
    }

    // Eliminar tokens expirados (opcional - para limpieza)
    public function limpiarTokensExpirados() {
        $query = "DELETE FROM " . $this->table_tokens . " WHERE expiracion < NOW()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>