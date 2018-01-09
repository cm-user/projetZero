<?php

namespace CM\EngravingBundle\Controller;

use CM\EngravingBundle\EngravingBundle;
use CM\EngravingBundle\Repository\OrderRegulatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use CM\EngravingBundle\Entity\Session;
use CM\EngravingBundle\Entity\Picture;
use Symfony\Component\HttpKernel\Exception;
use \PDO;
use Symfony\Component\HttpFoundation\Response;
use \ZipArchive;


class ViewController extends Controller
{
    /**
     * @Route("/", name="view_engraving")
     */
    public function indexAction()
    {
        return $this->render('EngravingBundle:view:index.html.twig');
    }

    /**
     * @Route("/new-picture/json", name="view_new_picture_json", options={"expose"=true})
     */
    public function NewPictureAction()
    {
        //http://doc.prestashop.com/download/attachments/720902/CRUD%20Tutorial%20EN.pdf
        $em = $this->getDoctrine()->getManager();
        $array_state = [2, 3, 4, 31]; //tableau contenant les "bons" etats des commandes
        $time = 0; //temps en minutes pour effectuer les gravures

        $persta = $this->get('iq2i_prestashop_web_service')->getInstance('my_prestashop_1');

//        $result = $persta->get(array(
//            "resource" => "orders",
//            "filter[id]" => '[230000, 250000]',
//            "display" => '[id]',
//        ));

        //recherche de la dernière image dans la bdd locale
        $last_picture= $this->get('engraving.repository.picture')->FindLast()[0];

//        $result = json_decode(json_encode((array)$result), TRUE);

//        //cherche l'id max
//        $id_max = end($result['orders']['order']); //récupère dernière ligne du tableau
//        $id_max = $id_max['id']; //récupère uniquement l'id

        //récupération du régulateur de commande
        $regulator = $em->getRepository('EngravingBundle:OrderRegulator')->find(1);
        $diff = $regulator->getNumber(); //valeur du regulateur
        $id_min = $last_picture->getName() - $diff;
        $id_max = $last_picture->getName() + $diff;

        //récupère les X dernières commandes
//        $id_min = $id_max - $diff;
        $result = $persta->get(array(
            "resource" => "orders",
            "filter[id]" => '[' . $id_min . ',' . $id_max . ']',
            "display" => '[id,id_cart,current_state,date_add]',
        ));

        $result = json_decode(json_encode((array)$result), TRUE);

        $array_id_order = $result['orders']['order']; //récupère  les commandes

        $pictures = $this->get('engraving.repository.picture')->findAll();
        //parcourt de toutes les commandes
        foreach ($array_id_order as $id_order) {
            //vérifie si l'état de la commande est adéquat
            if(in_array($id_order['current_state'], $array_state)){
                //vérifie si cette commande n'est pas déjà en bdd
                $pictures = $this->get('engraving.repository.picture')->FindAllByName($id_order['id']);
                //si elle n'est pas en bdd, on l'ajoute
                if (sizeof($pictures) == 0) {
                    //vérifie la présence d'objet à graver
                    $result_config_cart = $persta->get(array(
                        "resource" => "config_carts",  //trouver l'acces
                        "filter[id_cart]" => '[' . $id_order['id_cart'] . ']',
                        "display" => '[id,id_product,quantity]',
                    ));

                    $result_config_cart = json_decode(json_encode((array)$result_config_cart), TRUE);

                    //si il y a une bien une gravure on va la rajoute à notre bdd
                    if (isset($result_config_cart['config_carts']['config_cart'])) {

                        //Si il n'y a qu'un seul produit gravé dans le panier
                        if (isset($result_config_cart['config_carts']['config_cart']['id'])) {

                            $id_config = $result_config_cart['config_carts']['config_cart']['id'];
                            $id_product = $result_config_cart['config_carts']['config_cart']['id_product'];
                            $quantity = $result_config_cart['config_carts']['config_cart']['quantity'];

                            //ajoute en fonction de la quantite
                            for ($i = 0; $i < $quantity; $i++) {
                                $new_picture = new Picture();
                                $new_picture->setName($id_order['id'])->setIdProduct($id_product)->setIdConfig($id_config);
                                $new_picture->setPathJpg($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.jpg');
                                $new_picture->setPathPdf($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.pdf');
                                $new_picture->setEtat($id_order['current_state']);
                                $new_picture->setDatePresta($id_order['date_add']);
                                $new_picture->setChecked(0);

                                //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
                                $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);

                                if ($category != "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
                                    $new_picture->setCategory($category); //renseigne la categorie
                                    $new_picture->setTime($category->getTime()); //rentre la durée
                                }
//                                $time += $new_picture->getTime();
                                $em->persist($new_picture);
                            }
                        } //sinon on doit récupérer chaque produit
                        else {
                            foreach ($result_config_cart['config_carts']['config_cart'] as $key => $config_cart) {

                                $id_config = $result_config_cart['config_carts']['config_cart'][$key]['id'];
                                $id_product = $result_config_cart['config_carts']['config_cart'][$key]['id_product'];
                                $quantity = $result_config_cart['config_carts']['config_cart'][$key]['quantity'];

                                //ajoute en fonction de la quantite
                                for ($i = 0; $i < $quantity; $i++) {
                                    $new_picture = new Picture();
                                    $new_picture->setName($id_order['id'])->setIdProduct($id_product)->setIdConfig($id_config);
                                    $new_picture->setPathJpg($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.jpg');
                                    $new_picture->setPathPdf($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.pdf');
                                    $new_picture->setEtat($id_order['current_state']);
                                    $new_picture->setDatePresta($id_order['date_add']);
                                    $new_picture->setChecked(0);

                                    //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
                                    $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);

                                    if ($category != "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
                                        $new_picture->setCategory($category); //renseigne la categorie
                                        $new_picture->setTime($category->getTime()); //rentre la durée
                                    }

//                                    $time += $new_picture->getTime();
                                    $em->persist($new_picture);
                                }
                            }
                        }
                    }
                }
                else {
                    foreach($pictures as $picture){
//                        $this->UpdateState($picture); //Mise à jour de l'état et de la catégorie si l'image est déjà en bdd
//                        $time += $picture->getTime(); //incrémente le compteur de temps
                    }
                }
            }
        }
        $em->flush();

        //on récupère les images qui n'ont pas de session
        $Images = $this->get('engraving.repository.picture')->findAllNewPicture();

        //appel de cette fonction pour trier par etat tout en gardant le tri par catégorie et en gardant uniquement les etats 3 et 4
        $array_images_orderByEtat = ViewController::orderByEtat($Images);

        $formatted = [];
        foreach ($array_images_orderByEtat as $image) {

                //renseigne le nom et l'alias si la catégorie existe
                if($image->getCategory() != null){
                    $name_category = $image->getCategory()->getSurname();
//                $alias_category = $image->getCategory()->getAlias();
                }
                else {
                    $name_category = "NoCategory";
//                $alias_category = "NoAlias";
                }

                $formatted[] = [
                    'id' => $image->getId(),
                    'name' => $image->getName(),
                    'path-jpg' => $image->getPathJpg(),
                    'path-pdf' => $image->getPathPdf(),
                    'name_category' => $name_category,
//                'alias_category' => $alias_category,
                    'id_product' => $image->getIdProduct(),
                    'etat' => $image->getEtat(),
                    'date' => $image->getDatePresta(),
                    'check' => $image->getChecked(),
                ];
                $time += $image->getTime(); //somme le temps de gravure de chaque image
        }
        $formatted[] = ['temps' => $time]; //ajout du temps au tableau
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/ongoing-picture/json", name="view_ongoing_picture_json", options={"expose"=true})
     */
    public function OngoingPictureAction()
    {
        $em = $this->getDoctrine()->getManager();
        $time = 0; //temps en minutes pour effectuer les gravures
        $array_category = array('NoCategory' => 0); //tableau associatif pour gérer les différents compteurs de chaque catégorie
        $session = new Session();
        $session->setName((new \DateTime())->format('Y-m-d H:i:s')); //creation d'une nouvelle session avec la date et l'heure comme nom        \Doctrine\Common\Util\Debug::dump($session);

        //on remplit le tableau associatif avec les catégories
        $categories = $this->get('engraving.repository.category')->findAll();
        foreach ($categories as $cat) {
            $array_category[$cat->getSurname()] = 0;
        }

        $pictures = $this->get('engraving.repository.picture')->findAllNewPictureChecked();//récupère les nouvelles gravures qui ont été checked

        //appel de cette fonction pour trier par etat tout en gardant le tri par catégorie et en gardant uniquement les etats 3 et 4
//        $array_images_orderByEtat = ViewController::orderByEtat($pictures);


//        $pictures_uncheck = $this->get('engraving.repository.picture')->findAllNewPictureUnChecked();//récupère les gravures qui ont étaient décochées
//        //remet le checked à 1 pour chaque image
//        foreach ($pictures_uncheck as $picture){
//            $picture->setChecked(1);
//            $em->persist($picture);
//        }

        $formatted = [];
        foreach ($pictures as $picture) {

            //si l'etat n'est pas en cours de livraison ou en cours de préparation on ne traite pas l'image
            if ($picture->getEtat() == 3 || $picture->getEtat() == 4  ) {

                //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
                $id_product = $picture->getIdProduct();
                $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);

                if ($category == "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
                    $array_category['NoCategory']++; //incremente le compteur
                    $surname = 'NoCategory(' . $array_category['NoCategory'] . ')';
                } else {
                    $picture->setSession($session); //renseigne la session
                    $picture->setUpdatedAt(new \DateTime());

//                    $name_category = $category->getSurname();
//                    $array_category[$name_category]++;
                    $picture->setCategory($category); //renseigne la categorie
//                    $surname = $name_category . '(' . $array_category[$name_category] . ')';
//                    $picture->setSurname($surname);

                    $time += $category->getTime(); //incremente le compteur de temps

                    $formatted[] = [
                        'id' => $picture->getId(),
                        'name' => $picture->getName(),
                        'path-jpg' => $picture->getPathJpg(),
                        'path-pdf' => $picture->getPathPdf(),
//                        'surname' => $picture->getSurname(),
                        'etat' => $picture->getEtat(),
                    ];

                    $em->persist($picture);
                }
            }

            //enregistrement de la nouvelle session et des images
            $em->persist($session);

        }

        $session->setTimeTotal($time); //enregistre le temps total de gravure pour cette session
        $em->persist($session);
        $formatted[] = ['temps' => $time];

        $em->flush();

        return new JsonResponse($formatted);
    }

    //fonction pour pour mettre à jour l'état des gravures si celles-ci sont en cours de préparation
    private function UpdateState($picture)
    {
        $persta = $this->get('iq2i_prestashop_web_service')->getInstance('my_prestashop_1');
        $em = $this->getDoctrine()->getManager();
        $name = $picture->getName();

                //recherche de l'état actuel de chaque commande
            $result = $persta->get(array(
                "resource" => "orders",
                "filter[id]" => '[' . $name. ']',
                "display" => '[current_state]',
            ));

            $result = json_decode(json_encode((array)$result), TRUE);
            $order_state = $result['orders']['order']['current_state']; //récupère l'état

            //on met à jour notre bdd
                $images = $this->get('engraving.repository.picture')->FindAllByName($name);//récupère les nouvelles gravures
                foreach ($images as $image){
                    $image->setEtat($order_state); //maj de l'etat
//                    $image->setChecked(0); //remet le check à 1 comme valeur par défaut
                    $em->persist($image);
                }

        //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
        $id_product = $picture->getIdProduct();
        $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);

        if (sizeof($category) != "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
            $picture->setCategory($category); //renseigne la categorie
            $picture->setTime($category->getTime()); //rentre la durée

            $em->persist($picture);
        }

        $em->flush();
        return "";
    }

    /**
     * @Route("/download", name="download_pdf_gravure", options={"expose"=true})
     */
    public function download1Action()
    {
        $em = $this->getDoctrine()->getManager();
        $id_max_session = $this->get('engraving.repository.session')->findByMaxId(); //sélection de la dernière session
        $session = $this->get('engraving.repository.session')->findOneById($id_max_session);
        $array_category = array('NoCategory' => 0); //tableau associatif pour gérer les différents compteurs de chaque catégorie

//        $images = $session->getPictures();
        $images = $this->get('engraving.repository.picture')->findAllPictureMachineLaser($session->getId());//récupère les nouvelles gravures pour la machine ML Laser

        //on remplit le tableau associatif avec les catégories
        $categories = $this->get('engraving.repository.category')->findAll();
        foreach ($categories as $cat) {
            $array_category[$cat->getSurname()] = 0;
        }

        foreach ($images as $image){
            $category = $image->getCategory();
            $name_category = $category->getSurname();
            $array_category[$name_category]++;
            $surname = $name_category . '(' . $array_category[$name_category] . ')';
            $image->setSurname($surname);
            $em->persist($image);
        }
        $em->flush();

        //vérification que chaque image soit liée à une machine
//        foreach ($images as $image){
//            if($image->getMachine() == null){ //si aucune machine liée renvoi d'un message d'erreur
//                return new Response("error");
//            }
//        }

        $fichier = 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.zip';
        $chemin = "gravure/"; // emplacement de votre fichier .pdf

        $zip = new ZipArchive();
        if ($zip->open($chemin . $fichier) == TRUE)
            if ($zip->open($chemin . $fichier, ZipArchive::CREATE) === true) {

//                chmod( $fichier, 0777);

                foreach ($images as $image) {
//                    var_dump($image);
                    $category = $image->getCategory();                   
                    if ($category != null) {
                        $directory = $category->getFolder(); //nom du dossier associé à la catégorie

                        if(get_headers($image->getPathPdf())[0] == "HTTP/1.1 200 OK"){ //vérifie que l'url existe bien
                            $current = file_get_contents($image->getPathPdf()); //recupere contenu du fichier
//                            var_dump($current);

                        }
                        else { //s'il y a un problème avec le pdf de la gravure
                            $fichier_txt = fopen($chemin . 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt', 'a');
                            $current = "";
                            fputs($fichier_txt, "le produit " . $image->getName() . " n'a pas de pdf" . "\r\n");
                        }
                        $folder_file = $chemin . $image->getSurname() . '.pdf'; // nommage du fichier + son extension et choix du repertoire
                        file_put_contents($folder_file, $current); //creation du fichier au bon repertoire
                        $file = $image->getSurname() . '.pdf';
                        $zip->addFile($chemin . $file, $directory . '/' . $file); //Ajout du fichier au ZIP
                    }
                }
                //vérifie que le fichier txt existe avant de l'ajouter au ZIP
                if(file_exists($chemin . 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt')){
                    $zip->addFile($chemin . 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt', 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt'); //Ajout du fichier au ZIP
                    fclose($fichier_txt);
                }

                // Et on referme l'archive.
                $zip->close();
            } else {
                echo 'Impossible d&#039;ouvrir &quot;Zip.zip&quot;';
            }

//        $content = file_get_contents($chemin . $fichier);
//        if($content === false){
            //partie téléchargement
            $response = new Response();
            $response->setContent(file_get_contents($chemin . $fichier));
            $response->headers->set('Content-Type', 'application/zip'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
            $response->headers->set('Content-Transfer-Encoding', 'Binary');
            $response->headers->set('Content-Length', filesize($chemin . $fichier));
            $response->headers->set('Content-disposition', 'filename=GRAVURE.zip' );
            ob_end_clean();
            self::clearFolder($chemin);
//        }
//        else {
//            $response = "";
//        }


        return $response;

    }

    /**
     * Supprime le contenu d'un dossier
     * sans supprimer le dossier lui-même
     */
    private function clearFolder($folder)
    {
        // 1 ouvrir le dossier
        $dossier = opendir($folder);
        //2)Tant que le dossier est pas vide
        while ($fichier = readdir($dossier)) {
            //3) Sans compter . et ..
            if ($fichier != "." && $fichier != "..") {
                //On selectionne le fichier et on le supprime
                $Vidage = $folder . $fichier;
                unlink($Vidage);
            }
        }
        //Fermer le dossier vide
        closedir($dossier);
    }


//    /**
//     * Récupère les détails du produit sélectionné
//     * @Route("/{id}/order/test", name="faulty_prestashop_order_test", options={"expose"=true})
//     * @return JsonResponse
//     */
//    public function TestindexorderAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $array_state = [2, 3, 4, 30, 31];
//        $persta = $this->get('iq2i_prestashop_web_service')->getInstance();
//        $result = $persta->get(array(
//            "resource" => "orders",
//            "filter[id]" => '[1,3000000]',
//            "display" => '[id]',
//        ));
//
////        $result = json_encode((array)$result);
//        $result = json_decode(json_encode((array)$result), TRUE);
//
////        $result["orders"]["order"][0] = json_decode($result["orders"]["order"][0], true);
//
//
//        //cherche l'id max
//        $id_max = end($result['orders']['order']); //récupère dernière ligne du tableau
//        $id_max = $id_max['id']; //récupère uniquement l'id
////        \Doctrine\Common\Util\Debug::dump(file_exists(print_r($id_max-100)));
//
//
//        $repository = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository('EngravingBundle:OrderRegulator')
//        ;
//        $regulator = $em->getRepository('EngravingBundle:OrderRegulator')->find(1);
//        var_dump($regulator->getNumber());
//
//        //récupère les X dernières commandes
//        $id_min = $id_max - 5000;
//        $result = $persta->get(array(
//            "resource" => "orders",
//            "filter[id]" => '[' . $id_min . ',' . $id_max . ']',
//            "display" => '[id,id_cart,current_state]',
//        ));
//
//        $result = json_decode(json_encode((array)$result), TRUE);
//        $array_id_order = $result['orders']['order']; //récupère  les commandes
//
//
//        foreach ($array_id_order as $id_order) {
////            printf($id_order['current_state']);
//            if($id_order['current_state'] == 2){
//                printf("good");
//            }
////            if(in_array($id_order['current_state'], $array_state)){
////                printf("bon etat");
////                var_dump($id_order);
////            }
////            else {
////                printf("mauvais etat");
////                var_dump($id_order);
////            }
////
//        }
//
//        $picture = $this->get('engraving.repository.picture')->findOneByIdOrder($id);
//        var_dump($picture);
//        if ($picture == "") {
//            return new Response("vide !");
//        } else {
//            return new Response($picture);
//        }
//
//        return new JsonResponse("Enregistrement en bdd local");
//
//    }

//    /**
//     * Récupère les détails du produit sélectionné
//     * @Route("/{id}/order", name="faulty_prestashop_order", options={"expose"=true})
//     * @return JsonResponse
//     */
//    public function indexorderAction($id)
//    {
//        //http://doc.prestashop.com/download/attachments/720902/CRUD%20Tutorial%20EN.pdf
//        $em = $this->getDoctrine()->getManager();
//        $array_state = [2, 3, 4, 30, 31]; //tableau contenant les "bons" etats des commandes
//
//        $persta = $this->get('iq2i_prestashop_web_service')->getInstance();
//
//        $result = $persta->get(array(
//            "resource" => "orders",
//            "filter[id]" => '[1,3000000]',
//            "display" => '[id]',
//        ));
//
//        $result = json_decode(json_encode((array)$result), TRUE);
//
//        //cherche l'id max
//        $id_max = end($result['orders']['order']); //récupère dernière ligne du tableau
//        $id_max = $id_max['id']; //récupère uniquement l'id
//
//        //récupère les X dernières commandes
//        $id_min = $id_max - 50;
//        $result = $persta->get(array(
//            "resource" => "orders",
//            "filter[id]" => '[' . $id_min . ',' . $id_max . ']',
//            "display" => '[id,id_cart,current_state]',
//        ));
//
//        $result = json_decode(json_encode((array)$result), TRUE);
//        $array_id_order = $result['orders']['order']; //récupère  les commandes
//
//        $pictures = $this->get('engraving.repository.picture')->findAll();
//        //parcourt de toutes les commandes
//        foreach ($array_id_order as $id_order) {
//            //vérifie si l'état de la commande est adéquat
//            if(!in_array($id_order['current_state'], $array_state)){
//                printf("bon etat");
//                var_dump($id_order);
//                //vérifie si cette commande n'est pas déjà en bdd
//                $picture = $this->get('engraving.repository.picture')->findOneByIdOrder($id_order['id']);
//
//                //si elle n'est pas en bdd, on l'ajoute
//                if ($picture == "") {
//                    //vérifie la présence d'objet à graver
//                    $result_config_cart = $persta->get(array(
//                        "resource" => "config_carts",  //trouver l'acces
//                        "filter[id_cart]" => '[' . $id_order['id_cart'] . ']',
//                        "display" => '[id,id_product,quantity]',
//                    ));
//
//                    $result_config_cart = json_decode(json_encode((array)$result_config_cart), TRUE);
//
//                    //si il y a une bien une gravure on va la rajoute à notre bdd
//                    if (isset($result_config_cart['config_carts']['config_cart'])) {
//
//
//                        //Si il n'y a qu'un seul produit gravé dans le panier
//                        if (isset($result_config_cart['config_carts']['config_cart']['id'])) {
//
//                            $id_config = $result_config_cart['config_carts']['config_cart']['id'];
//                            $id_product = $result_config_cart['config_carts']['config_cart']['id_product'];
//                            $quantity = $result_config_cart['config_carts']['config_cart']['quantity'];
//
//                            //ajoute en fonction de la quantite
//                            for ($i = 0; $i < $quantity; $i++) {
//                                $new_picture = new Picture();
//                                $new_picture->setName($id_order['id'])->setIdProduct($id_product)->setIdConfig($id_config);
//                                $new_picture->setPathJpg($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.jpg');
//                                $new_picture->setPathPdf($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.pdf');
//
//                                //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
//                                $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);
//
//                                if ($category != "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
//                                    $new_picture->setCategory($category); //renseigne la categorie
//                                }
//
//                                $em->persist($new_picture);
//                            }
//                        } //sinon on doit récupérer chaque produit
//                        else {
//                            foreach ($result_config_cart['config_carts']['config_cart'] as $key => $config_cart) {
//
//                                $id_config = $result_config_cart['config_carts']['config_cart'][$key]['id'];
//                                $id_product = $result_config_cart['config_carts']['config_cart'][$key]['id_product'];
//                                $quantity = $result_config_cart['config_carts']['config_cart'][$key]['quantity'];
//
//                                //ajoute en fonction de la quantite
//                                for ($i = 0; $i < $quantity; $i++) {
//                                    $new_picture = new Picture();
//                                    $new_picture->setName($id_order['id'])->setIdProduct($id_product)->setIdConfig($id_config);
//                                    $new_picture->setPathJpg($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.jpg');
//                                    $new_picture->setPathPdf($this->getParameter('url_directory_engraving') . $id_config . '-' . $id_product . '.pdf');
//
//                                    //////Methode récupérer l'id produit de chaque image pour trouver sa categorie///////
//                                    $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);
//
//                                    if ($category != "") { //si la requête est vide (id produit est introuvable) cette image ne sera pas traité
//                                        $new_picture->setCategory($category); //renseigne la categorie
//                                    }
//
//                                    $em->persist($new_picture);
//                                }
//
//                            }
//                        }
//                    }
//                }
//            }
//            else {
//                printf("mauvais etat");
//                var_dump($id_order);
//            }
//        }
//        $em->flush();
//
//        return new JsonResponse("Enregistrement en bdd local");
//    }

    /**
     * @Route("/ongoing-picture/laser/{id}", name="view_ongoing_picture_laser", options={"expose"=true})
     */
    public function OngoingPictureLaserAction($id)
    {

        $picture = $this->get('engraving.repository.picture')->findOneById($id);
        $picture->setMachine("ML_Laser");

        $this->get('engraving.repository.picture')->save($picture);

        return new Response("passage machine laser");
    }

    /**
     * @Route("/ongoing-picture/gravograph/{id}", name="view_ongoing_picture_gravograph", options={"expose"=true})
     */
    public function OngoingPictureGravographAction($id)
    {

        $picture = $this->get('engraving.repository.picture')->findOneById($id);
        $picture->setMachine("Gravograph");

        $this->get('engraving.repository.picture')->save($picture);

        return new Response("passage machine gravograph");
    }

    /**
     * @Route("/ongoing-picture/uncheck/{id}", name="view_ongoing_picture_uncheck", options={"expose"=true})
     */
    public function OngoingPictureUncheckAction($id)
    {

        $picture = $this->get('engraving.repository.picture')->findOneById($id);

        //modifie la valeur du check en fonction de son etat précédent
        if($picture->getChecked() == 1){
            $picture->setChecked(0);
        }
        else {
            $picture->setChecked(1);
        }

        $this->get('engraving.repository.picture')->save($picture);

        return new Response("image uncheck");
    }

    //fonction pour trier les images par etat en gardant leur tri par catégorie
    private function orderByEtat($images){
        $array = [];
        $array_etat_4 = [];
        $array_etat_3 = [];
        $array_null_etat_4 = [];
        $array_null_etat_3 = [];
        $categories_old = $images[0]->getCategory()->getSurname();

//        var_dump($categories_old);

//        $category = $this->get('engraving.repository.category')->findOneByIdProduct($id_product);
//
//        foreach ($categories as $cat) {
//            $array_category[$cat->getSurname()] = 0;
//        }

        //parcours des images
        for($i=0;$i<count($images);$i++){
            if($images[$i]->getCategory() != null){ //vérifie que l'image a bien une catégorie lié
                if($images[$i]->getCategory()->getSurname() != $categories_old){ //vérifie qu'il s'agit de la même catégorie à chaque itération
                    $categories_old = $images[$i]->getCategory()->getSurname(); //renseigne la nouvelle catégorie
                    $array = array_merge($array, $array_etat_4, $array_etat_3); //concaténation des tableaux suivant les différents états
                    if($images[$i]->getEtat() == 4){
                        $array_etat_4 = [$images[$i]];
                    }
                    if($images[$i]->getEtat() == 3){
                        $array_etat_3 = [$images[$i]];
                    }
                }
                else {
                    if($images[$i]->getEtat() == 4){
                        $array_etat_4[] = $images[$i];
                    }
                    if($images[$i]->getEtat() == 3){
                        $array_etat_3[] = $images[$i];
                    }
                }
            }
            else {
                if($images[$i]->getEtat() == 4){
                    $array_null_etat_4[] = $images[$i];
                }
                if($images[$i]->getEtat() == 3){
                    $array_null_etat_3[] = $images[$i];
                }
            }
        }
        $array = array_merge($array, $array_etat_4, $array_etat_3); //merge pour la dernière catégorie
        $array = array_merge($array, $array_null_etat_4, $array_null_etat_3); //merge des images sans catégories

        return $array;
//        $array_category = array('NoCategory' => 0); //tableau associatif pour gérer les différentes catégories
//
//        foreach ($categories as $cat) {
//            $array_category[$cat->getSurname()] = 0;
//        }
//
//
//        $name_category = $category->getSurname();
//        $array_category[$name_category]++;
//        $picture->setCategory($category); //renseigne la categorie
//        $surname = $name_category . '(' . $array_category[$name_category] . ')';

    }

    /**
     * @Route("/new-picture-paid/json", name="view_picture_paid_json", options={"expose"=true})
     */
    public function NewPicturePaidAction()
    {
        $time = 0; //temps en minutes pour effectuer les gravures

        //on récupère les images qui n'ont pas de session
        $Images = $this->get('engraving.repository.picture')->findAllNewPicturePaid();

        $formatted = [];
        foreach ($Images as $image) {

            //renseigne le nom et l'alias si la catégorie existe
            if($image->getCategory() != null){
                $name_category = $image->getCategory()->getSurname();
            }
            else {
                $name_category = "NoCategory";
            }

            $formatted[] = [
                'id' => $image->getId(),
                'name' => $image->getName(),
                'path-jpg' => $image->getPathJpg(),
                'path-pdf' => $image->getPathPdf(),
                'name_category' => $name_category,
                'id_product' => $image->getIdProduct(),
                'etat' => $image->getEtat(),
                'date' => $image->getDatePresta(),
                'check' => $image->getChecked(),
            ];
            $time += $image->getTime(); //somme le temps de gravure de chaque image
        }
        $formatted[] = ['temps' => $time]; //ajout du temps au tableau
        return new JsonResponse($formatted);
    }

}
