 <?php
  // session_start();
  //se connecter a la base de donnees
  require_once("bdd-connect.php");
  include_once("fonctions.php");


  //recuperer les messages
  $stmt = $bdd->prepare("SELECT 
                        c.idCollabo,
                        c.nom, 
                        c.prenom, 
                        c.imageProfil, 
                        c.imageProfilType, 
                        c.job, 
                        m1.contenu, 
                        m1.statut, 
                        m1.dateAction,
                        m1.id_emetteur
                      FROM 
                        collabo c
                      JOIN 
                        (SELECT 
                            m.id_emetteur, 
                            m.contenu, 
                            m.statut, 
                            m.dateAction,
                            m.id_recepteur
                        FROM 
                            message m
                        WHERE 
                            m.id_recepteur = ?
                        ORDER BY 
                            m.dateAction DESC
                        ) m1 
                      ON 
                        m1.id_emetteur = c.idCollabo
                      GROUP BY 
                        m1.id_emetteur
                      ORDER BY 
                        MAX(m1.statut) DESC, 
                        MAX(m1.dateAction) DESC
                      LIMIT 3;

    ");
  $stmt->execute([$idCollabo]);
  $mes_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // var_dump($mes_messages);
  // exit;

  // Récupérer le nombre de notifications non lues
  $stmt = $bdd->prepare("SELECT COUNT(*) FROM message WHERE id_recepteur = ? AND statut = ? ");
  $stmt->execute([$idCollabo, "1"]);
  $nbNewMes = $stmt->fetchColumn();


  ?>


 <li class="nav-item dropdown">

   <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
     <i class="bi bi-chat-left-text"></i>
     <span class="badge bg-success badge-number"><?php echo isset($nbNewMes) ? $nbNewMes : '0'; ?></span>
   </a><!-- End Messages Icon -->

   <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
     <li class="dropdown-header">
       You have <?php echo isset($nbNewMes) ? $nbNewMes : '0'; ?> new messages
       <a href="pages-messages.php?"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
     </li>

     <?php if (isset($mes_messages) && count($mes_messages) > 0) : ?>
       <?php foreach ($mes_messages as $key => $message) : ?>
         <?php
          // Gérer la photo de profil
          if (!empty($message['imageProfil']) && !empty($message['imageProfilType'])) {
            // Afficher l'image
            $imageData = base64_encode($message['imageProfil']); // Convertir les données de l'image en base64
            $imageType = $message['imageProfilType']; // Récupérer le type de l'image
            $srcProfil_auteurMes = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
          }
          //recuperer le nombre de messages non lus venant de cet utilisateur
          $stmt = $bdd->prepare("SELECT * FROM message WHERE id_recepteur = ? AND id_emetteur = ? AND statut = ?");
          $stmt->execute([$idCollabo, $message['id_emetteur'], "1"]);
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if ($result)
            $nbMesNonLu = count($result);
          //la date du dernier message
          $stmt = $bdd->prepare("SELECT dateAction FROM message WHERE id_emetteur = ? ORDER BY dateAction DESC LIMIT 1");
          $stmt->execute([$message['id_emetteur']]);
          $derniereDate = $stmt->fetchColumn();
          ?>
         <li>
           <hr class="dropdown-divider">
         </li>

         <li class="message-item dropdown-item">
           <a href="pages-messages.php?id_emetteur=<?php echo $message['idCollabo']; ?>">
             <img src="<?php echo isset($srcProfil_auteurMes) ? $srcProfil_auteurMes : "assets/img/profile-inconnu.png";
                        unset($srcProfil_auteurMes); ?>" alt="image profil">
             <div>
               <h4><?php echo htmlspecialchars($message['prenom'] . " " . $message['nom']); ?></h4>
               <!-- <p><?php
                        $messageContent = htmlspecialchars($message['contenu']);
                        echo substr($messageContent, 0, 59);
                        echo (strlen($messageContent) > 60) ? '...' : ''; ?>
               </p> -->
               <p style="<?php echo (isset($nbMesNonLu) && $nbMesNonLu > 0) ? 'font-weight:bold' : ''; ?>"><?php echo (isset($nbMesNonLu) && $nbMesNonLu > 0) ? $nbMesNonLu . ' message' : '';
                                                                                                          echo (isset($nbMesNonLu) && $nbMesNonLu > 1) ? 's non lus' : '';
                                                                                                          unset($nbMesNonLu); ?></p>
               <p><?php echo dateAction($derniereDate); ?></p>
             </div>
           </a>
         </li>
       <?php endforeach; ?>
     <?php endif; ?>

     <li>
       <hr class="dropdown-divider">
     </li>
     <li class="dropdown-footer">
       <a href="pages-messages.php">Show all messages</a>
     </li>

   </ul><!-- End Messages Dropdown Items -->

 </li><!-- End Messages Nav -->