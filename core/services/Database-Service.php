<?php

namespace ZEngine\Core\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private ?PDO $connection = null;
    private array $config;
    private array $queryLog = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function connect(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $driver = $this->config['driver'] ?? 'mysql';
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 3306;
        $database = $this->config['database'] ?? '';
        $username = $this->config['username'] ?? 'root';
        $password = $this->config['password'] ?? '';
        $charset = $this->config['charset'] ?? 'utf8mb4';

        try {
            $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}";
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }

        return $this->connection;
    }

    public function query(string $sql, array $params = []): array
    {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $this->queryLog[] = ['sql' => $sql, 'params' => $params];

        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $this->queryLog[] = ['sql' => $sql, 'params' => $params];

        return $stmt->rowCount();
    }

    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->execute($sql, array_values($data));

        return (int) $this->connect()->lastInsertId();
    }

    public function update(string $table, array $data, array $where): int
    {
        $set = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        $whereClause = implode(' AND ', array_map(fn($key) => "{$key} = ?", array_keys($where)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$whereClause}";

        return $this->execute($sql, array_merge(array_values($data), array_values($where)));
    }

    public function delete(string $table, array $where): int
    {
        $whereClause = implode(' AND ', array_map(fn($key) => "{$key} = ?", array_keys($where)));
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";

        return $this->execute($sql, array_values($where));
    }

    public function select(string $table, array $columns = ['*'], array $where = [], string $orderBy = '', int $limit = 0): array
    {
        $cols = implode(', ', $columns);
        $sql = "SELECT {$cols} FROM {$table}";

        $params = [];
        if (!empty($where)) {
            $whereClause = implode(' AND ', array_map(fn($key) => "{$key} = ?", array_keys($where)));
            $sql .= " WHERE {$whereClause}";
            $params = array_values($where);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->query($sql, $params);
    }

    public function find(string $table, int|string $id, string $primaryKey = 'id'): ?array
    {
        $result = $this->select($table, ['*'], [$primaryKey => $id], '', 1);
        return $result[0] ?? null;
    }

    public function beginTransaction(): bool
    {
        return $this->connect()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connect()->commit();
    }

    public function rollback(): bool
    {
        return $this->connect()->rollBack();
    }

    public function getQueryLog(): array
    {
        return $this->queryLog;
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table);
    }
}

class QueryBuilder
{
    private DatabaseService $db;
    private string $table;
    private array $wheres = [];
    private array $orders = [];
    private ?int $limitValue = null;
    private ?int $offsetValue = null;
    private array $bindings = [];

    public function __construct(DatabaseService $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function where(string $column, mixed $operator, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetValue = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }

        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }

        return $this->db->query($sql, $this->bindings);
    }

    public function first(): ?array
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $result = $this->db->query($sql, $this->bindings);
        return (int) ($result[0]['count'] ?? 0);
    }
}
