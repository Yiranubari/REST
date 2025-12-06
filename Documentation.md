# REST API 

This document provides a comprehensive analysis of the `REST` project, a simple PHP-based RESTful API designed for managing blog posts and categories. The API follows a standard structure, separating configuration, core business logic (models), and API endpoints (controllers). The detailed explanations below are intended to serve as a reference for understanding the codebase's architecture and functionality.

## 1. Project Structure

The project is organized into three main directories, each serving a distinct purpose:

| Directory | Purpose |
| --- | --- |
| `api/` | Contains the API endpoints (PHP files) that handle incoming HTTP requests (GET, POST, PUT, DELETE) and return JSON responses. These files act as the controllers in the application. |
| `core/` | Contains the core PHP classes (`Post`, `Category`) that encapsulate the business logic and database interaction using the PDO extension. These files act as the models. |
| `includes/` | Contains configuration and initialization files necessary for the application to run. |

## 2. File-by-File Analysis

### 2.1. Configuration and Initialization

#### `REST/includes/config.php`

This file is responsible for establishing the database connection and setting essential PDO attributes.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 3-6 | `$db_user = 'root'; ... $db_host = 'localhost';` | Defines the database credentials. **Security Note:** Hardcoding credentials like this is a security risk; environment variables or a separate configuration file should be used in a production environment. |
| 8 | `$db = $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);` | Creates a new PDO (PHP Data Objects) instance, establishing the connection to the MySQL database. The connection object is assigned to both `$db` and `$pdo`. |
| 12-14 | `$db->setAttribute(...)` | Sets essential PDO attributes: `PDO::ATTR_EMULATE_PREPARES, false` ensures native prepared statements are used for better security; `PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION` configures PDO to throw exceptions on errors, which is the recommended way to handle database errors. |
| 17 | `define('APP_NAME', 'REST API');` | Defines a global constant for the application name. |

#### `REST/core/initialize.php`

This file defines global constants for directory paths and includes the necessary configuration and core classes, acting as the application's bootstrap file.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 2-5 | `defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR); ...` | Defines constants for file system navigation (`DS` for directory separator, `SITE_ROOT`, `INC_PATH`, `CORE_PATH`) to ensure portability across different operating systems. |
| 8 | `require_once(INC_PATH . DS . 'config.php');` | Includes the database configuration file. |
| 11-12 | `require_once(CORE_PATH . DS . 'post.php'); ...` | Includes the core business logic classes (`Post` and `Category`). |

### 2.2. Core Business Logic (Models)

#### `REST/core/post.php`

This file defines the `Post` class, which handles all CRUD (Create, Read, Update, Delete) operations for blog posts in the database.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 3-15 | `class Post { ... public $created_at; }` | Defines the `Post` class with private properties for the database connection (`$conn`) and table name (`$table`), and public properties to hold post data (e.g., `$id`, `$title`, `$body`). |
| 18-21 | `public function __construct($db)` | The constructor accepts the database connection object (`$db`) and assigns it to the internal `$conn` property. |
| 23-42 | `public function read()` | **Read All Posts:** Executes a `LEFT JOIN` query to fetch all posts along with their corresponding category names. It prepares and executes the statement, returning the PDO statement object for iteration in the API endpoint. |
| 44-72 | `public function read_single()` | **Read Single Post:** Executes a `LEFT JOIN` query to fetch a single post based on its `id`. It uses a positional placeholder (`?`) and `bindParam` for secure query execution. The fetched data is then assigned to the public properties of the `Post` object. |
| 74-97 | `public function create()` | **Create Post:** Executes an `INSERT` query using named placeholders (`:title`, etc.). It first sanitizes the input data using `htmlspecialchars(strip_tags(...))` to prevent XSS attacks. It binds the sanitized values and executes the query, returning `true` on success or `false` on failure. |
| 100-125 | `public function update()` | **Update Post:** Executes an `UPDATE` query, similar to `create()`, but includes the `id` in the `WHERE` clause to target a specific record. It also sanitizes all input fields, including the `id`. |
| 127-143 | `public function delete()` | **Delete Post:** Executes a `DELETE` query based on the post `id`. It sanitizes the `id` input before binding and executing the statement. |

#### `REST/core/category.php`

This file defines the `Category` class, which currently only handles reading all categories from the database.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 3-11 | `class Category { ... public $created_at; }` | Defines the `Category` class with properties for database connection, table name, and category data (`$id`, `$name`, `$created_at`). |
| 14-17 | `public function __construct($db)` | The constructor accepts and assigns the database connection object. |
| 19-31 | `public function read()` | **Read All Categories:** Executes a simple `SELECT *` query to fetch all records from the `categories` table. It prepares and executes the statement, returning the PDO statement object. |

### 2.3. API Endpoints (Controllers)

All API files handle HTTP headers for CORS (Cross-Origin Resource Sharing) and content type, include the `initialize.php` file, instantiate the relevant core class, process input data, call the appropriate class method, and return a JSON response.

#### `REST/api/create.php`

Handles the creation of a new post. Expects a JSON payload via a `POST` request.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 4-7 | `header(...)` | Sets necessary headers: `Access-Control-Allow-Origin: *` for CORS, `Content-Type: application/json`, and `Access-Control-Allow-Methods: POST` to specify the allowed HTTP method. |
| 10 | `include_once '../core/initialize.php';` | Includes the bootstrap file to set up the environment and load classes. |
| 13 | `$post = new Post($db);` | Instantiates the `Post` object, passing the database connection `$db`. |
| 16 | `$data = json_decode(file_get_contents("php://input"));` | Reads the raw JSON data from the request body and decodes it into a PHP object. |
| 19-22 | `$post->title = $data->title; ...` | Assigns the data from the JSON payload to the public properties of the `$post` object. |
| 25-29 | `if ($post->create()) { ... }` | Calls the `create()` method on the `$post` object. If successful, it echoes a success JSON message; otherwise, it echoes a failure message. |

#### `REST/api/read.php`

Handles the retrieval of all posts. Expects a `GET` request.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 14 | `$result = $post->read();` | Calls the `read()` method to execute the query and get the result set. |
| 17 | `$num = $result->rowCount();` | Gets the number of rows returned. |
| 19-37 | `if ($num > 0) { ... } else { ... }` | Checks if any posts were found. If so, it iterates through the result set, builds an array of post items, and encodes the final array into a JSON response. **Note:** `html_entity_decode($body)` is used to reverse any HTML entity encoding that might have occurred during storage. |

#### `REST/api/read_single.php`

Handles the retrieval of a single post. Expects a `GET` request with the post `id` passed as a query parameter (e.g., `?id=1`).

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 14 | `$post->id = isset($_GET['id']) ? $_GET['id'] : die();` | Retrieves the `id` from the URL query string (`$_GET`). If the `id` is not set, the script terminates (`die()`). |
| 15 | `$post->read_single();` | Calls the `read_single()` method, which populates the `$post` object's properties with the fetched data. |
| 18-26 | `$post_arr = array(...)` | Creates an associative array from the populated `$post` object properties. |
| 29 | `print_r(json_encode($post_arr));` | Encodes the single post array into a JSON response and prints it. |

#### `REST/api/update.php`

Handles the update of an existing post. Expects a JSON payload via a `PUT` request.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 6 | `header('Access-Control-Allow-Methods: PUT');` | Specifies that this endpoint handles `PUT` requests. |
| 19 | `$post->id = $data->id;` | Retrieves the `id` from the JSON payload, which is required to identify the post to be updated. |
| 26-30 | `if ($post->update()) { ... }` | Calls the `update()` method. Returns a success or failure JSON message. |

#### `REST/api/delete.php`

Handles the deletion of a post. Expects a JSON payload containing the `id` via a `DELETE` request.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 6 | `header('Access-Control-Allow-Methods: DELETE');` | Specifies that this endpoint handles `DELETE` requests. |
| 19 | `$post->id = $data->id;` | Retrieves the `id` of the post to be deleted from the JSON payload. |
| 23-27 | `if ($post->delete()) { ... }` | Calls the `delete()` method. Returns a success or failure JSON message. |

#### `REST/api/read_all_categories.php`

Handles the retrieval of all categories. Expects a `GET` request.

| Lines | Code Block | Explanation |
| --- | --- | --- |
| 11 | `$post = new Category($db);` | **Note:** The variable name `$post` is misleading here; it should be `$category`. Instantiates the `Category` object. |
| 14 | `$result = $post->read();` | Calls the `read()` method from the `Category` class. |
| 19-34 | `if ($num > 0) { ... }` | Iterates through the category result set, builds an array of category items, and returns the final array as a JSON response. |

