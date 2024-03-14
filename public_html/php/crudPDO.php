<?php
class CrudPDO
{
    private $pdo;
    
    public function __construct($host, $dbname, $username, $password)
    {
        try {
            // Create a PDO instance for database connection
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password
            );
            // Set PDO to throw exceptions on errors
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception(
                "Database connection failed: " . $e->getMessage()
            );
        }
    }

    public function create($table, $data)
    {
        try {
            // Identify the timestamp column dynamically
            $timestamp_column = $this->findTimestampColumn($table, $data);
            // Check if the array has a timestamp column with a default value
            if (
                $timestamp_column &&
                isset($data[$timestamp_column]) &&
                $data[$timestamp_column] === "CURRENT_TIMESTAMP"
            ) {
                // Remove the timestamp column from the array
                unset($data[$timestamp_column]);
            }
            // Build the SQL INSERT query
            $fields = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO $table ($fields) VALUES ($values)";
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
            // Bind parameters and execute the statement
            $stmt->execute($data);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error creating record: " . $e->getMessage());
        }
    }

    private function findTimestampColumn($table, $data)
    {
        // Identify the possible names for a timestamp column in the provided table
        $possible_timestamp_columns = array_keys($data);
        // Check if any of the possible timestamp columns exist in the table
        foreach ($possible_timestamp_columns as $timestamp_column) {
            // Fetch the table columns from the database
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM $table");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Check if the timestamp column exists in the table
            foreach ($columns as $column) {
                if (
                    $column["Field"] === $timestamp_column &&
                    strtolower($column["Type"]) === "timestamp"
                ) {
                    return $timestamp_column;
                }
            }
        }
        // If none of the possible timestamp columns are found in the table, return null
        return null;
    }

    public function read($table)
    {
        try {
            // Build the SQL SELECT query
            $sql = "SELECT * FROM $table";
            // Execute the query and fetch all records
            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            throw new Exception("Error reading records: " . $e->getMessage());
        }
    }

    public function readCustom($sql, $params = [])
    {
        try {
            // Prepare the custom SQL query
            $stmt = $this->pdo->prepare($sql);
            // Bind parameters and execute the statement
            $stmt->execute($params);
            // Fetch all records
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            throw new Exception("Error reading records: " . $e->getMessage());
        }
    }

    public function update($table, $id, $data)
    {
        try {
            // Build the SQL UPDATE query
            $set = implode(
                ", ",
                array_map(function ($key) {
                    return "$key = :$key";
                }, array_keys($data))
            );
            $sql = "UPDATE $table SET $set WHERE id = :id";
            // Add the id to the data array
            $data["id"] = $id;
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
            // Bind parameters and execute the statement
            $stmt->execute($data);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error updating record: " . $e->getMessage());
        }
    }

    public function delete($table, $id)
    {
        try {
            // Start a transaction
            $this->pdo->beginTransaction();
            // Build the SQL DELETE query
            $sql = "DELETE FROM $table WHERE id = :id";
            // Prepare the statement
            $stmt = $this->pdo->prepare($sql);
            // Bind parameters and execute the statement
            $stmt->execute(["id" => $id]);
            // Commit the transaction
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $this->pdo->rollBack();
            // Propagate the exception
            throw new Exception("Error deleting record: " . $e->getMessage());
        }
    }
} ?>

