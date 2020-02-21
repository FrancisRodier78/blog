<!-- index.php -->

<p style="text-align: center">Il y a actuellement <?= $nombreNews ?> news. En voici la liste :</p>
 
<table>
  <tr><th>Auteur</th><th>Titre</th><th>Date de création</th><th>Dernière modification</th><th>Action</th></tr>
<?php
foreach ($listeNews as $news)
{
  echo '<tr><td> {{ news.user_id }} </td><td>', $news['titre'], '</td><td>le ', $news['dateCreation']->format('d/m/Y à H\hi'), '</td><td>', ($news['dateCreation'] == $news['dateModif'] ? '-' : 'le '.$news['dateModif']->format('d/m/Y à H\hi')), '</td><td><a href="news-update-', $news['id'], '.html"><img src="/images/update.png" alt="Modifier" /></a> <a href="news-delete-', $news['id'], '.html"><img src="/images/delete.png" alt="Supprimer" /></a></td></tr>', "\n";
}
?>

{% for new in listeNews %}
        {{ new.user_id }}, {{ news.titre }}, {{ news.dateCreation|date('d/m/Y H:i', timezone="Europe/Paris") }}, {{ news.dateModif|date('d/m/Y H:i', timezone="Europe/Paris") }}, 
        <a href="news-update-', $news['id'], '.html"><img src="/images/update.png" alt="Modifier" /></a> <a href="news-delete-', $news['id'], '.html"><img src="/images/delete.png" alt="Supprimer" /></a></td>
{% endfor %}

</table>