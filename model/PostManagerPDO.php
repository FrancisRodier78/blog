<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Blog\model;
use \PDO;
use \Blog\model\PostManager;
use \Blog\model\Entity\Post;

class PostManagerPDO extends PostManager
{
    /**
     * Attribut contenant l'instance représentant la BDD.
     *
     * @var PDO
     */
    protected $db;

    /**
     * Constructeur étant chargé d'enregistrer l'instance de PDO dans l'attribut $db.
     *
     * @param $db PDO Le DAO
     *
     * @return void
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @see PostManager::countPost()
     */
    public function countPost()
    {
        return $this->db->query('SELECT COUNT(*) FROM post')->fetchColumn();
    }

    /**
     * @see PostManager::deletePost()
     *
     * @param mixed $idPost
     */
    public function deletePost($idPost)
    {
        $this->db->exec('DELETE FROM post WHERE id = '.(int) $idPost);
    }

    /**
     * @see PostManager::getListPosts()
     *
     * @param mixed $debut
     * @param mixed $limite
     */
    public function getListPosts($debut = -1, $limite = -1)
    {
        $sql = 'SELECT P.id, U.loggin AS auteur, P.titre, P.dateCreation, P.dateModif, P.chapo, P.content FROM post AS P INNER JOIN user AS U ON U.id = P.user_id ORDER BY P.id DESC';

        // On vérifie l'intégrité des paramètres fournis.
        if (-1 !== $debut || -1 !== $limite) {
            $sql .= ' LIMIT '.(int) $limite.' OFFSET '.(int) $debut;
        }

        $requete = $this->db->query($sql);

        // Par FETCH_CLASS on récupère un tableau d'objet et non un tableau de table.
        // Par FETCH_PROPS_LATE on force l'exécution du constructeur avant celui du contrôleur.
        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Blog\model\Post');

        $listePost = $requete->fetchAll();

        // On parcourt notre liste de post pour pouvoir placer des instances de DateTime en guise de dates.
        // On passe d'une date typé SQL à une date typé DateTime.
        foreach ($listePost as $post) {
            $post->setDateCreation(new \DateTime($post->getDateCreation()));
            $post->setDateModif(new \DateTime($post->getDateModif()));
        }

        $requete->closeCursor();

        return $listePost;
    }

    /**
     * @see PostManager::getUniquePost()
     *
     * @param mixed $idPost
     */
    public function getUniquePost($idPost)
    {
        $requete = $this->db->prepare('SELECT P.id, P.user_id, U.name, U.firstname, P.titre, P.dateCreation, P.dateModif, P.chapo, P.content FROM post AS P INNER JOIN user AS U ON U.id = P.user_id WHERE P.id = :id');
        $requete->bindValue(':id', (int) $idPost, PDO::PARAM_INT);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Blog\model\Post');

        $post = $requete->fetch();

        $post->setDateCreation(new \DateTime($post->getDateCreation()));
        $post->setDateModif(new \DateTime($post->getDateModif()));

        return $post;
    }

    /**
     * @see PostManager::addPost()
     */
    protected function addPost(Post $post)
    {
        $requete = $this->db->prepare('INSERT INTO post(user_id, titre, dateCreation, dateModif, chapo, content) VALUES(:user, :titre, NOW(), NOW(), :chapo, :content)');

        $user = 1;
        $requete->bindValue(':user', $user);
        $requete->bindValue(':titre', $post->getTitre());
        $requete->bindValue(':chapo', $post->getChapo());
        $requete->bindValue(':content', $post->getContent());

        $requete->execute();
    }

    /**
     * @see PostManager::updatePost()
     */
    protected function updatePost(Post $post)
    {
        $requete = $this->db->prepare('UPDATE post SET titre = :titre, dateModif = NOW(), chapo = :chapo, content = :content WHERE id = :id');

        $requete->bindValue(':id', $post->getid(), PDO::PARAM_INT);
        $requete->bindValue(':titre', $post->getTitre());
        $requete->bindValue(':chapo', $post->getChapo());
        $requete->bindValue(':content', $post->getContent());

        $requete->execute();
    }
}
