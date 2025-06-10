<?php
class Project {
    public $id, $title, $description, $image, $image_path, $user_id, $created_at;
    public $author, $author_email;

    public function __construct($data) {
        $this->id           = $data['id'];
        $this->title        = $data['title'];
        $this->description  = $data['description'];
        $this->image        = $data['image'] ?? null;
        $this->image_path   = $data['image_path'] ?? null;
        $this->user_id      = $data['user_id'];
        $this->created_at   = $data['created_at'];
        $this->author       = $data['author'];
        $this->author_email = $data['author_email'];
    }

    
    public static function fetchAllWithAuthor($conn) {
        $sql = "
            SELECT p.*, u.name AS author, u.email AS author_email
            FROM projects p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ";
        $res = mysqli_query($conn, $sql);
        $out = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $out[] = new Project($row);
        }
        return $out;
    }

    public static function search($conn, $query) {
        $sql = "SELECT p.*, u.name AS author, u.email AS author_email 
                FROM projects p 
                JOIN users u ON p.user_id = u.id
                WHERE p.title LIKE ? OR p.description LIKE ?";
        
        $stmt = $conn->prepare($sql);
        $search = '%' . $query . '%';
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $out = [];
        while ($row = $result->fetch_assoc()) {
            $out[] = new Project($row);
        }
        return $out;
    }
}
?>