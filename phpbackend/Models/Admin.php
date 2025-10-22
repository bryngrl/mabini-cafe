    <?php

    class Admin{

    private $conn;
    private $table = 'admins';
    public $id;
    public $username;
    public $password;
    public $email;


    public function __construct($db)
        {
            $this->conn = $db ;
        }


        // GET ALL Admins
        public function getAll(){
            $stmt = $this->conn->prepare("SELECT * FROM ".$this->table);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

            //GET Admins BY ID
        public function getById($id)
        {
            $stmt = $this->conn->prepare("SELECT id,email,username FROM ".$this->table." WHERE id =:id");
            $stmt->bindParam(":id",$id);
            $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // fetch single row

        }


            // CREATE Admin
        public function create() {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)"
            );
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password); // hashed password
            return $stmt->execute();
        }


        //update Admin
    public function update()
    {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET username=:username, email=:email WHERE id=:id"
        );

        $stmt->bindParam(":username",$this->username);
        $stmt->bindParam(":email",$this->email);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

        // DELETE admin
        public function delete() {
            $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=:id");
            $stmt->bindParam(":id", $this->id);
            return $stmt->execute();
        }


        // Find Admin by Email (for login)
        public function findByEmail($email) {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE email=:email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    }