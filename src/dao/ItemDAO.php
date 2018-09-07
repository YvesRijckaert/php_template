<?php

require_once( __DIR__ . '/DAO.php');

class ItemDAO extends DAO {

  public function selectAll(){
    $sql = "SELECT * FROM `items`";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function selectById($id){
    $sql = "SELECT * FROM `items` WHERE `id` = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  
  public function selectAllWithImageLimit($limit = 10){ //select them all (maar als er geen limit is doe dan 10)
    $sql = "SELECT * FROM `items` WHERE `image` != '' LIMIT :limit";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

}
