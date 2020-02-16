<?php
// UsersManagerPDO.php

namespace Model;
 
use \Entity\Users;
 
class UsersManagerPDO extends UsersManager
{
  protected function add(Users $users)
  {
    $requete = $this->dao->prepare('INSERT INTO user SET role_id = :role_id, name = :name, firstname = :firstname, loggin = :loggin, password = :password, email = :email, picture = :picture, grip = :grip, dateRegistration = : NOW()');

    $requete->bindValue(':role_id', $users->role_id());
    $requete->bindValue(':name', $users->name());
    $requete->bindValue(':firstname', $users->firstname());
    $requete->bindValue(':loggin', $users->loggin());
    $requete->bindValue(':password', $users->password());
    $requete->bindValue(':email', $users->email());
    $requete->bindValue(':picture', $users->picture());
    $requete->bindValue(':grip', $users->grip());
 
    $requete->execute();
  }
 
  public function count()
  {
    return $this->dao->query('SELECT COUNT(*) FROM users')->fetchColumn();
  }
 
  public function delete($id)
  {
    $this->dao->exec('DELETE FROM users WHERE id = '.(int) $id);
  }
 
  public function getList($debut = -1, $limite = -1)
  {
    $sql = 'SELECT id, role_id, name, firstname, loggin, password, email, picture, grip, dateRegistration FROM users ORDER BY id DESC';

    if ($debut != -1 || $limite != -1) {
      $sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
    }
 
    $requete = $this->dao->query($sql);
    $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Users');
 
    $listeUsers = $requete->fetchAll();
 
    foreach ($listeUsers as $users)
    {
      $users->setDateCreation(new \DateTime($users->dateRegistration()));
    }
 
    $requete->closeCursor();
 
    return $listeUsers;
  }
 
  public function getUnique($id)
  {
    $requete = $this->dao->prepare('SELECT id, role_id, name, firstname, loggin, password, email, picture, grip, dateRegistration FROM users WHERE id = :id');
    $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
    $requete->execute();
 
    $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Users');
 
    if ($users = $requete->fetch()) {
      $users->setdateCreation(new \DateTime($users->dateRegistration()));
 
      return $users;
    }
 
    return null;
  }
 
  protected function modify(Users $users)
  {
    $requete = $this->dao->prepare('UPDATE users SET role_id = :role_id, name = :name, firstname = :firstname, loggin = :loggin, password = :password, email = :email, picture = :picture, grip = :grip WHERE id = :id');
                                                    
 
    $requete->bindValue(':role_id', $users->role_id());
    $requete->bindValue(':name', $users->name());
    $requete->bindValue(':firstname', $users->firstname());
    $requete->bindValue(':loggin', $users->loggin());
    $requete->bindValue(':password', $users->password());
    $requete->bindValue(':email', $users->email());
    $requete->bindValue(':picture', $users->picture());
    $requete->bindValue(':grip', $users->grip());
 
    $requete->execute();
  }
}