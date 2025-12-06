<?php

class Category
{
    private $conn;
    private $table = 'categories';

    // Category Properties
    public $id;
    public $name;
    public $created_at;

    // Constructor with DB connection 
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Get Posts
    public function read()
    {
        // Create query
        $query = 'SELECT
        *
        FROM
        ' . $this->table;
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        // Execute query
        $stmt->execute();
        return $stmt;
    }
}
