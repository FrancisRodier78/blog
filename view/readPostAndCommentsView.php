<?php 
if ($_COOKIE['ketto'] == $_SESSION['ketto']) {
    // C'est reparti pour un tour
    $ketto = session_id().microtime().rand(0,9999999999);
    $ketto = hash('sha512', $ketto);
    $_COOKIE['ketto'] = $ketto;
    $_SESSION['ketto'] = $ketto;
} else {
    // On détruit la session
    $_SESSION = array();
    session_destroy();
    header('location:index.php');
}
?>

<?php $title = 'Lecture d\'un post et de ses commentaires'; ?>

<?php ob_start(); ?>
    <!-- Affichage du post (varA) -->
    <p>Par <em> <?php echo $varA->getUserId(); ?> </em>, créer le <?php echo $varA->getDateCreation()->format('d/m/Y à H\hi'); ?> modifier le <?php echo $varA->getDateModif()->format('d/m/Y à H\hi'); ?> </p>
    <h2><?php echo $varA->getTitre(); ?></h2>
    <p><?php echo nl2br($varA->getContent()); ?></p>

    <p class="inputComment">Saisir un commentaire</p>
    <form action="." method="post">
        <p style="text-align: center" class="inputComment">
            Texte du commentaire : <input type="text" rows="8" cols="60" name="content" value="" /><br />
            <input type="hidden" name="idPost" value="<?php echo $varA->getId(); ?>" />
            <input type="submit" value="Ajouter un nouveau commentaire" name="add comment" />
        </p>
    </form>

    <div class="post">
    <?php
    foreach ($listA as $comment) {
        ?>
        <!-- Affichage des commentaires du post -->
        <p><label>Auteur &nbsp;: &nbsp;</label><?php echo nl2br($comment->getAuteur()); ?><label>,&nbsp; Date du commentaire &nbsp;: &nbsp;</label><?php echo nl2br($comment->getDateComment()); ?></p>
        <p><?php echo nl2br($comment->getContent()); ?></p>
    <?php
    }
    ?>
    </div>

    <p><a href="?come_back_list_posts">Retourner à l'accueil</a></p>
<?php $content = ob_get_clean(); ?>

<?php require 'template.php'; ?>
