# Mabini Cafe

This project is a web application for Mabini Cafe.

## Getting Started

1. **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/mabini-cafe.git
    cd mabini-cafe
    ```

2. **Install dependencies:**
    - Make sure you have [XAMPP](https://www.apachefriends.org/) installed.
    - Place the project folder inside your XAMPP `htdocs` directory.

3. **Database Setup:**
    - The database file is **not included** in the repository (it's in `.gitignore`).
    - Obtain the database SQL file from the project maintainer.
    - Import the SQL file into your local MySQL server using phpMyAdmin or the MySQL CLI.
    - Alternatively, you can create the database using the following PHP code. **Make sure to place this file inside the `config` folder and name it `database.php`.** Copy and paste the code below, update the credentials as needed, and run it once:

    ```php
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mabini_cafe";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists.";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->close();
    ?>
    ```

4. **Run the Application:**
    - Start Apache and MySQL from the XAMPP control panel.
    - Open your browser and go to:  
      `http://localhost/mabini-cafe/`

## Notes

- Ensure your database credentials in the project match your local setup.
- If you encounter issues, check file permissions and server logs.
