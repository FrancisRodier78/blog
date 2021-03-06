<?php
// NewsController.php

namespace App\Backend\Modules\News;
 
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;

class NewsController extends BackController
{
  public function executeDelete(HTTPRequest $request)
  {
    $newsId = $request->getData('id');
 
    $this->managers->getManagerOf('News')->delete($newsId);
    $this->managers->getManagerOf('Comments')->deleteFromNews($newsId);
 
    $this->app->user()->setFlash('La news a bien été supprimée !');
 
    $this->app->httpResponse()->redirect('.');
  }
 
  public function executeDeleteComment(HTTPRequest $request)
  {
    $comment =  $this->managers->getManagerOf('Comments')->get($request->getData('id'));

    $this->managers->getManagerOf('Comments')->delete($request->getData('id'));
 
    $this->app->user()->setFlash('Le commentaire a bien été supprimé !');

    $this->app->httpResponse()->redirect('/news-' . $comment->new_id() . '.html');

  }
 
  public function executeIndex(HTTPRequest $request)
  {
    $_SESSION['precedent'] = 'admin';

    $manager = $this->managers->getManagerOf('News');
    $manager2 = $this->managers->getManagerOf('Users');
    $manager3 = $this->managers->getManagerOf('Users');
    $manager4 = $this->managers->getManagerOf('Comments');

    return $this->render('backend/BackendNewsIndex.html', ['title' => 'Gestion des news',
                                                                    'listeNews' => $manager->getList(),
                                                                    'nombreNews' => $manager->count(),
                                                                    'listeUsers' => $manager2->getList(),
                                                                    'nombreUsers' => $manager2->count(),
                                                                    'utilisateur' => $manager3->getUnique($_SESSION['utilisateur-id']),
                                                                    'ListCommentsEnAtt' => $manager4->getListAtt(),
                                                                    'ListCommentsRefuse' => $manager4->getListRefuse()]);
  }
 
  public function executeInsert(HTTPRequest $request)
  {
    $news = $this->processForm($request);
    $user_id = $_SESSION['utilisateur-id'];

    return $this->render('backend/BackendNewsInsert.html', ['title' => 'Ajout d\'une news', 'News' => $news, 'User_id' => $user_id]);
  }
 
  public function executeSave(HTTPRequest $request)
  {
    $news = new News;
    $news->setUser_id($request->postData('user_id'));
    $news->setTitre($request->postData('titre'));
    $news->setChapo($request->postData('chapo'));
    $news->setContent($request->postData('content'));

    if ($request->postExists('id')) {
      $news->setId($request->postData('id'));
    }

    $this->managers->getManagerOf('News')->save($news);

    if ($news->isNew()) {
        $this->app->httpResponse()->redirect('/');
    } else {
        $this->app->httpResponse()->redirect('.');
    }
  }

  public function executeUpdate(HTTPRequest $request)
  {
    $news = $this->processForm($request);
    $user_id = $_SESSION['utilisateur-id'];

    return $this->render('backend/BackendNewsUpdate.html', ['title' => 'Modification d\'une news', 'News' => $news, 'User_id' => $user_id]);
  }
 
  public function executeUpdateComment(HTTPRequest $request)
  {
    $comment = $this->managers->getManagerOf('Comments')->get($request->getData('id'));

    return $this->render('backend/BackendNewsUpdateComment.html', ['title' => 'Modification d\'un commentaire', 'Comment' => $comment, 'Id' => $request->getData('id')]);
  }
 
  public function executeCommentSave(HTTPRequest $request)
  {
    $comment = new Comment;
    $comment->setId($request->postData('comment_id'));
    $comment->setNew_id($request->postData('new_id'));
    $comment->setUser_id($request->postData('user_id'));
    $comment->setContent($request->postData('content'));
    $comment->setEtat($request->postData('etat'));

    $this->managers->getManagerOf('Comments')->save($comment);

    if ($_SESSION['precedent'] == 'show') {
        // Cela veut dire que la modification vient de Frontend NewsController executeShow
        $this->app->httpResponse()->redirect('/news-' . $request->postData('new_id') . '.html');
    } else {
        // $_SESSION['precedent'] == 'admin')
        // Cela veut dire que la modification vient de backend NewsController executeUpdateComment
        $this->app->httpResponse()->redirect('.');
    }
  }

  public function processForm(HTTPRequest $request)
  {
    // L'identifiant de la news est transmis si on veut la modifier
    if ($request->getExists('id')) {
    $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
    } else {
    $news = new News;
    }

    return $news;
  }
}