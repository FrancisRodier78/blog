<?php
// UsersManagerPDO.php

namespace Model;
 
use \Entity\Users;
 
class UsersManagerPDO extends UsersManager
{
  protected function add(Users $users)
  {
    $requete = $this->dao->prepare('INSERT INTO users SET role_id = :role_id, name = :name, firstname = :firstname, loggin = :loggin, password = :password, email = :email, picture = :picture, grip = :grip, dateRegistration = NOW()');

    $requete->bindValue(':role_id', 'Visiteur');
    $requete->bindValue(':name', $users->name());
    $requete->bindValue(':firstname', $users->firstname());
    $requete->bindValue(':loggin', $users->loggin());
    $requete->bindValue(':password', $users->password());
    $requete->bindValue(':email', $users->email());
    $requete->bindValue(':picture', ' ');
    $requete->bindValue(':grip', ' ');
 
    $requete->execute();
  }
 
  public function count()
  {
    return $this->dao->query('SELECT COUNT(*) FROM users')->fetchColumn();
  }
 
  public function delete($id)
  {
    $requete = $this->dao->prepare('DELETE FROM users WHERE id = :id');
    $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
    $requete->execute();
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
      $users->setDateRegistration(new \DateTime($users->dateRegistration()));
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
      $users->setdateRegistration(new \DateTime($users->dateRegistration()));
 
      return $users;
    }
 
    return null;
  }
 
  public function exist($loggin, $password)
  {
    $requete = $this->dao->prepare('SELECT id, role_id, name, firstname, loggin, password, email, picture, grip, dateRegistration FROM users WHERE loggin = :loggin AND password = :password');
    $requete->bindValue(':loggin', $loggin, \PDO::PARAM_STR);
    $requete->bindValue(':password', $password, \PDO::PARAM_STR);
    $requete->execute();
 
    $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Users');
 
    if ($users = $requete->fetch()) {
      $users->setdateRegistration(new \DateTime($users->dateRegistration()));
      return $users;
    }

    return false;
  }

    public function existLoggin($loggin)
    {
        $requete = $this->dao->prepare('SELECT id, role_id, name, firstname, loggin, password, email, picture, grip, dateRegistration FROM users WHERE loggin = :loggin');
        $requete->bindValue(':loggin', $loggin, \PDO::PARAM_STR);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Users');

        if ($users = $requete->fetch()) {
            $users->setdateRegistration(new \DateTime($users->dateRegistration()));
            return $users;
        }

        return false;
    }

    protected function modify(Users $users)
  {
    if ($users->password() !== '') {
        $requete = $this->dao->prepare('UPDATE users SET role_id = :role_id, name = :name, firstname = :firstname, loggin = :loggin, password = :password, email = :email, picture = :picture, grip = :grip WHERE id = :id');
    } else {
        $requete = $this->dao->prepare('UPDATE users SET role_id = :role_id, name = :name, firstname = :firstname, loggin = :loggin, email = :email, picture = :picture, grip = :grip WHERE id = :id');
    }

    $requete->bindValue(':id', $users->id());
    $requete->bindValue(':role_id', $users->roleId());
    $requete->bindValue(':name', $users->name());
    $requete->bindValue(':firstname', $users->firstname());
    $requete->bindValue(':loggin', $users->loggin());

    if ($users->password() !== '') {
        $password = $users->password().'Je suis une brute';
        $password = sha1($password);
        $requete->bindValue(':password', $password);
    }

    $requete->bindValue(':email', $users->email());
    $requete->bindValue(':picture', ' ');
    $requete->bindValue(':grip', ' ');
 
    $requete->execute();
  }
}