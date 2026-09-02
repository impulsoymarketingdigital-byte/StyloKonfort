<?php
class Query extends Conexion
{
    private PDO $con;

    public function __construct()
    {
        parent::__construct();
        $this->con = parent::conect();
    }

    /**
     * Ejecuta un SELECT y retorna una sola fila.
     * 
     * @param string $sql   Consulta SQL con placeholders (? o :nombre)
     * @param array  $datos Valores para los placeholders (por defecto vacío)
     * @return array|false  Array asociativo con la fila, o false si no existe
     */
    public function select(string $sql, array $datos = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($datos);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ejecuta un SELECT y retorna todas las filas.
     * 
     * @param string $sql   Consulta SQL con placeholders (? o :nombre)
     * @param array  $datos Valores para los placeholders (por defecto vacío)
     * @return array        Array de arrays asociativos (vacío si no hay resultados)
     */
    public function selectAll(string $sql, array $datos = []): array
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($datos);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ejecuta un INSERT, UPDATE o DELETE.
     * Retorna 1 si tuvo éxito, 0 si falló.
     * 
     * @param string $sql   Consulta SQL con placeholders
     * @param array  $datos Valores para los placeholders
     * @return int          1 = éxito, 0 = fallo
     */
    public function save(string $sql, array $datos): int
    {
        $stmt = $this->con->prepare($sql);
        return $stmt->execute($datos) ? 1 : 0;
    }

    /**
     * Ejecuta un INSERT y retorna el ID insertado.
     * Retorna el lastInsertId() si tuvo éxito, 0 si falló.
     * 
     * @param string $sql   Consulta SQL con placeholders
     * @param array  $datos Valores para los placeholders
     * @return int          ID del registro insertado, o 0 si falló
     */
    public function insertar(string $sql, array $datos): int
    {
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute($datos)) {
            return (int) $this->con->lastInsertId();
        }
        return 0;
    }

    /**
     * Inicia una transacción de base de datos.
     */
    public function beginTransaction(): void
    {
        $this->con->beginTransaction();
    }

    /**
     * Confirma una transacción de base de datos.
     */
    public function commit(): void
    {
        $this->con->commit();
    }

    /**
     * Revierte una transacción de base de datos.
     */
    public function rollBack(): void
    {
        $this->con->rollBack();
    }
}