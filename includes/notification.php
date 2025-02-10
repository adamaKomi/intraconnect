<?php

//se connecter a la base de donnees
require_once("bdd-connect.php");
include_once("fonctions.php");

// Récupérer les notifications
$stmt = $bdd->prepare(
                "SELECT n.*, nc.statut
                FROM notification n
                JOIN notificationCollabo nc ON nc.idNotification = n.id
                WHERE nc.idCollabo = ?
                ORDER BY nc.statut DESC, nc.dateAction DESC LIMIT 3"
            );
$stmt->execute([$idCollabo]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre de notifications non lues
$stmt = $bdd->prepare( "SELECT * FROM notificationcollabo WHERE idCollabo = ? AND statut = ? ");
$stmt->execute([$idCollabo, "1"]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nbNewNotifs = count($result);

// var_dump($notifications);
// var_dump($result);

?>



<li class="nav-item dropdown">

    <a class="nav-link nav-icon" href="ww.com" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span class="badge bg-primary badge-number"><?php echo $nbNewNotifs; ?></span>
    </a><!-- End Notification Icon -->

    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
        <li class="dropdown-header">
            You have <?php echo $nbNewNotifs; ?> new notifications
            <a href="pages-notifications.php"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
        </li>
        <?php if (isset($notifications)) : ?>
            <?php foreach ($notifications as $key => $notification) : ?>
                <li>
                    <hr class="dropdown-divider">
                </li>

                <li class="notification-item">
                    <!-- <i class="bi bi-exclamation-circle text-warning"></i> -->
                    <!-- verifier la nature de l'element concerné et adapter le lien -->
                    <div>
                        <a href="<?php echo $notification['lien']."&idNotification=".$notification['id']; ?>" style="color: black;">
                            <?php if ($notification['statut'] == 1) : ?>
                                <h4><?php echo substr($notification['titre'], 0, 29);
                                echo (strlen($notification['titre']) > 30) ? "..." : "";?></h4>
                            <?php else : ?>
                                <p><?php echo substr($notification['titre'], 0, 29);
                                echo (strlen($notification['titre']) > 30) ? "..." : "";?></p>
                            <?php endif ?>
                            <p><?php echo substr($notification['contenu'], 0, 40);
                                echo (strlen($notification['contenu']) > 41) ? "..." : ""  ?></p>
                        </a>
                        <p style="display:flex; flex-direction: row; justify-content: space-between; margin-top: 10px; ">
                            <span><?php echo dateAction($notification['dateAction']); ?></span>
                            <!-- marquer comme non lu -->
                            <?php if ($notification['statut'] == 0) : ?>
                                <a href="traitement/annuler-element.php?idNotification=<?php echo $notification['id']; ?>&amp;action=marquer-notification-comme-non-lu" class="btn btn-warning btn-sm ml-3">Marquer comme non lu</a>
                            <?php else : ?>
                                <a href="traitement/annuler-element.php?idNotification=<?php echo $notification['id']; ?>&amp;action=marquer-notification-comme-lu" class="btn btn-primary btn-sm">Marquer comme lu</a>
                            <?php endif; 
                            //recuperer la page actuelle pour la redirection
                            $_SESSION['page_precedente'] = $_SERVER['REQUEST_URI']; ?>
                        </p>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>


        <li>
            <hr class="dropdown-divider">
        </li>
        <li class="dropdown-footer">
            <a href="pages-notifications.php">Show all notifications</a>
        </li>

    </ul><!-- End Notification Dropdown Items -->

</li><!-- End Notification Nav -->