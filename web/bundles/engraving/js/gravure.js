
//masque les boutons download
// $("#btn_download").hide();
$("#btn_download2").hide();

//////////au clic du bouton une requête ajax se lance pour charger les nouvelles gravures en attente////////
function actualize() {
    $("#btn_actualize").addClass("btn-danger").removeClass("btn-info");  //changement de couleur du bouton
    var $elem = "<br>";
    var compteur = 0;  //compteur pour calculer quand placer les rows
    var nom_categorie = ""; //nom de la catégorie
    var old_nom_categorie = ""; //nom de la catégorie antérieur
    var compteur_ligne = 1 ; //compteur pour nombre de ligne pour chaque tableau
    $.ajax({

        url: Routing.generate('view_new_picture_json'),

        success: function (result) {
            console.log(result);
            $.each(result, function (key, val) {
                
                if(val['temps'] != undefined){
                    var newDateObj = addMinutes(new Date(), val['temps']).toString().substring(16,21);
                    $("#time_new_gravure").html("<h3> Temps estimé à " + val['temps'] +" minutes, fin prévue à " + newDateObj + "</h3><a><button id=\"btn_paid_accept\" class=\"btn btn-info\" onclick=\"displayPaidAccept();\">Voir les paiements acceptés</button></a>");
                }
                else if(val['msg'] != undefined) {
                    $("#msg_new_gravure").html("<br><div class=\"alert alert-danger\">Il n'y a pas de nouvelles gravures , patientez... ou pensez à augmenter le régulateur de commande</div>");
                }
                else {
                    // $elem += compteur % 4 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                    // $elem += "<div class=\"col-sm-3\" align=\"center\">";
                    // $elem += val['etat'] == 4 ? "<div class=\"alert alert-success\">" : "<div class=\"alert alert-light\" style=\"background-color:orange;\">"; //affiche une alert en fonction de l'état de la commande
                    //
                    // $elem += "<a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['path-jpg'] + " \" width=\'200\'></a>";
                    // // $elem += "<br>";
                    // $elem += "<div class=\"span12\" class=\"text-center\">";
                    // $elem += "<a href=\" " + val['path-pdf'] + "\">" + val['name'] + "</a>";
                    // $elem += "</div>";
                    // // $elem += "<br>";
                    //
                    // if (val['id_category'] == null) {
                    //     $elem += "<a class=\"btn btn-success\" id=\"add_" + val['id'] + "\" href=\"/projetZero/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a>";
                    // }
                    //
                    // $elem += "</div>";
                    // $elem += "</div>";
                    // $elem += compteur % 4 == 3 ? "</div>" : "";
                    // //modal
                    // $elem += "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Picture_" + val['id'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Picture_title_" + val['id'] + "\" aria-hidden=\"true\">";
                    // $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
                    // $elem += "<div class=\"modal-content\">";
                    // $elem += "<div class=\"modal-header\">";
                    // $elem += "<h5 class=\"modal-title\" id=\"Modal_Picture_title_" + val['id'] + "\"></h5>";
                    // $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
                    // $elem += "<span aria-hidden=\"true\">&times;</span>";
                    // $elem += "</button>";
                    // $elem += "</div>";
                    // $elem += "<div class=\"modal-body\">";
                    // $elem += "<img src=\"" + val['path-jpg'] + " \" width=\'750\'>";
                    // $elem += "</div>";
                    // $elem += "<div class=\"modal-footer\">";
                    // $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                    // $elem += "</div>";
                    // $elem += "</div>";
                    // $elem += "</div>";
                    // $elem += "</div>";

                    //à la première itération on recupère le nom de la catégorie
                    nom_categorie = val['name_category'];

                    //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                    if(nom_categorie != old_nom_categorie){
                        if(compteur != 0){
                            $elem += "</tbody> </table>";
                            $elem += "</div>";
                            $elem += compteur % 2 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                            compteur_ligne = 1; //remet le compteur de ligne à 1 avant de passer à la prochaine catégorie
                        }

                        $elem += compteur % 2 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                        $elem += "<div class=\"col-sm-6\" align=\"center\">";
                        $elem += "<h1><b>" + val['name_category'] + "</b></h1>"; //nom de la catégorie
                        $elem += "<table class=\"table\"> <thead> <tr> <th>Numéro</th> <th>Image</th> <th>Id Commande</th> <th>Prêt à graver</th>  <th>Date de création</th> </tr> </thead> <tbody>";

                        compteur++; //incrémentation du compteur
                    }

                    $elem += val['etat'] == 4 ? "<tr class=\"success\" style=\"background-color:lightgreen;\">" : "<tr class=\"light\" style=\"background-color:orange;\">"; //affiche une alert en fonction de l'état de la commande
                    $elem += "<td><h2>" + compteur_ligne + "</h2></td>";
                    $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['path-jpg'] + " \" width=\'100\'></a></td>";
                    $elem += "<td><h2>" + val['name'] + "<h2>" ;
                        if (val['name_category'] == "NoCategory") {
                            $elem += "<a class=\"btn btn-success\" id=\"add_" + val['id'] + "\" href=\"/projetZero2/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a>";
                        }
                    $elem += "</td>";
                    $elem += "<td><label class=\"checkbox-inline\">" ;
                    $elem += val['check'] == 1 ? "<input type=\"checkbox\" value=\"\" checked id=\"btg_checkbox_" + val['id'] +"\" onclick=\"UncheckPicture(" + val['id'] + ");\">" : "<input type=\"checkbox\" value=\"\" id=\"btg_checkbox_" + val['id'] +"\" onclick=\"UncheckPicture(" + val['id'] + ");\">" ;
                    $elem += "</label></td>";
                    $elem += "<td>" + val['date'] + "</td>";
                    $elem += "</tr>";

                    //modal
                    $elem += "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Picture_" + val['id'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Picture_title_" + val['id'] + "\" aria-hidden=\"true\">";
                    $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
                    $elem += "<div class=\"modal-content\">";
                    $elem += "<div class=\"modal-header\">";
                    $elem += "<h5 class=\"modal-title\" id=\"Modal_Picture_title_" + val['id'] + "\"></h5>";
                    $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
                    $elem += "<span aria-hidden=\"true\">&times;</span>";
                    $elem += "</button>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-body\">";
                    $elem += "<img src=\"" + val['path-jpg'] + " \" width=\'750\'>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-footer\">";
                    $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";

                    old_nom_categorie = val['name_category'];
                    compteur_ligne++;
                }


            });
            // $elem += "<br><br>";
            $("#new_gravure").html($elem);
            $("#btn_actualize").addClass("btn-info").removeClass("btn-danger");  //changement de couleur du bouton
        }
    });
}

//////Affiche les nouvelles gravures et propose de téléchager un dossier ZIP contenant ces images/////
function DisplayNewEngraving() {
    $("#btn_session").addClass("btn-danger").removeClass("btn-warning");  //changement de couleur du bouton

    $.ajax({

        url: Routing.generate('view_ongoing_picture_json'),

        success: function (result) {
            DisplayPicture(result);
            console.log(result);
        }
    });
    $("#btn_session").addClass("btn-warning").removeClass("btn-danger");  //changement de couleur du bouton
}

function DisplayPicture(data) {
    // $("#new_gravure").html(""); //efface les nouvelles gravures qui sont devenue en cours
    // $("#time_new_gravure").html(""); //efface le temps
    var array_id_picture = [];
    var compteur = 0 ;
    $elem = "<br>";
    $.each(data, function (key, val) {

        if(val['temps'] != undefined){
            var newDateObj = addMinutes(new Date(), val['temps']).toString().substring(16,21);
            $("#time_ongoing_gravure").html("<h3> Temps estimé à " + val['temps'] +" minutes, fin prévue à " + newDateObj + "</h3>");
        }
        else {
            array_id_picture.push(val['id']);//ajout de l'id de l'image dans le tableau
            $elem += compteur % 3 == 0 ? "<div class=\"row\">" : ""; //affiche une row toutes les 3 gravures
            $elem += "<div class=\"col-sm-4 alert alert-danger\" align=\"center\" id=\'click_machine_" + val['id'] + "\'>";
            // $elem += "<div id=\'click_machine_" + val['id'] + "\' class=\'alert alert-danger\'>";
            $elem += "<a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['path-jpg'] + " \" width=\'350\'></a>";
            $elem += "<div class=\"row\">";
            $elem += "<div class=\"col-sm-6\" >";
            $elem += "<br>";
            $elem += "<a href=\" " + val['path-pdf'] + "\">" + val['name'] + "</a>";
            // $elem += "<p>" + val['surname'] + "</p>";
            $elem += "</div>";
            $elem += "<div class=\"col-sm-6 text-left\" >";
            $elem += "<br>";
            $elem += "<div class=\"radio\"> <label><input id=\"mllaser_" + val['id'] + "\" type=\"radio\" name=\"machine_" + val['id'] + "\" size=\"30\" onclick=\'compteCase();\'>&nbsp;&nbsp;ML Laser</label></div>";
            $elem += "<div class=\"radio\"> <label><input id=\"gravograph_" + val['id'] + "\" type=\"radio\" name=\"machine_" + val['id'] + "\" size=\"20\" onclick=\'compteCase();\'>&nbsp;&nbsp;Gravograph</label></div>";
            $elem += "</div>";
            $elem += val['etat'] == 3 ? "<div class=\"col-sm-12\"><button class=\"btn btn-warning btn-block\" style=\"background-color:orange;\">Préparation de la caisse en cours</button></div>" : ""; //indique que cette gravure est en préparation en cours

            $elem += "</div>";
            $elem += "</div>";
            // $elem += "</div>";
            $elem += compteur % 3 == 2 ? "</div>" : "";

            //modal
            $elem += "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Picture_" + val['id'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Picture_title_" + val['id'] + "\" aria-hidden=\"true\">";
            $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
            $elem += "<div class=\"modal-content\">";
            $elem += "<div class=\"modal-header\">";
            $elem += "<h5 class=\"modal-title\" id=\"Modal_Picture_title_" + val['id'] + "\"></h5>";
            $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
            $elem += "<span aria-hidden=\"true\">&times;</span>";
            $elem += "</button>";
            $elem += "</div>";
            $elem += "<div class=\"modal-body\">";
            $elem += "<img src=\"" + val['path-jpg'] + " \" width=\'750\'>";
            $elem += "</div>";
            $elem += "<div class=\"modal-footer\">";
            $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
            $elem += "</div>";
            $elem += "</div>";
            $elem += "</div>";
            $elem += "</div>";

            compteur++; //incrémentation du compteur
        }
    });

    $elem += "</div><div class=\"row\"><button class=\"btn btn-warning\" onclick=\"ActualizeAndClean();\">Terminer la session</button></div>";

    $("#ongoing_gravure").html($elem);

    function compteCase(){
        nbCaseCochees = $('input:checked').length;
        console.log(nbCaseCochees);
    }
    $('radio').click(compteCase);

    console.log(array_id_picture);
    for (i = 0; i < array_id_picture.length; i++) { //Ajout des listeners pour chaque image

        $("#mllaser_" + array_id_picture[i]).on("click", function () {  //listener sur la checkbox ML Laser
            var id = $(this).attr("id");    //recupere l'id du bouton
            id = id.replace("mllaser_", ""); //recupere l'id de la branche uniquement
            $("#click_machine_" + id).addClass("alert-success").removeClass("alert-danger");  //ajout d'un cadre de couleur
            if( $(this).is(':checked') ){ //si la checkbox n'est pas deja check
                $(this).prop('checked',true);
                $("#gravograph_" + array_id_picture[i]).prop('checked', false); // Unchecks la checkbox gravograph
                $.ajax({
                    url: Routing.generate('view_ongoing_picture_laser', {id: id}),
                    success: function (result) {
                        console.log(result);
                    }
                });
            }
            else{
                $(this).prop('checked',false);
            }

        });

        $("#gravograph_" + array_id_picture[i]).on("click", function () {   //listener sur la checkbox Gravograph
            var id = $(this).attr("id");    //recupere l'id du bouton
            id = id.replace("gravograph_", ""); //recupere l'id de la branche uniquement
            $("#click_machine_" + id).addClass("alert-success").removeClass("alert-danger");  //ajout d'un cadre de couleur
            //enregistre dans la bdd la machine utilisée
            if( $(this).is(':checked') ){ //si la checkbox n'est pas deja check
                $(this).prop('checked',true);
                $("#mllaser_" + array_id_picture[i]).prop('checked', false); // Unchecks la checkbox laser
                $.ajax({
                    url: Routing.generate('view_ongoing_picture_gravograph', {id: id}), //enregistre en bdd
                    success: function (result) {
                        console.log(result);
                    }
                });
            }
            else{
                $(this).prop('checked',false);
            }

        });
    }

    actualize(); //appel de la fonction pour actualiser
}

//compte le nombre d'input coché
function compteCase(){
    nbCaseCochees = $('input:checked').length;
    nbCaseTotal = ($('input').length) / 2;
    if (nbCaseCochees == nbCaseTotal){ //si toutes les sont cochées
        // $("#btn_download").show(); //on affiche le bouton download en haut
        $("#btn_download2").show(); //on affiche le bouton download en bas
    }
}

//fonction pour ajouter des minutes à un objet date
function addMinutes(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
}

//fonction pour uncheck la gravure afin qu'elle ne soit pas prise en compte au lancement d'une nouvelle session
function UncheckPicture(id){
    $.ajax({
        url: Routing.generate('view_ongoing_picture_uncheck', {id: id}), //enregistre en bdd
        success: function (result) {
            console.log(result);
        }
    });
}

//fonction pour actualiser et effacer la session en cours
function ActualizeAndClean(){
    actualize(); //appel de la fonction pour actualiser
    $("#new_gravure").html(""); //efface les nouvelles gravures
    $("#ongoing_gravure").html(""); //efface le contenu de la div
}

//affiche les gravures avec le statut paiement accepté ou paiement validé
function displayPaidAccept(){
    $("#btn_paid_accept").addClass("btn-danger").removeClass("btn-info");  //changement de couleur du bouton
    var $elem = "<br>";
    var compteur = 0;  //compteur pour calculer quand placer les rows
    var nom_categorie = ""; //nom de la catégorie
    var old_nom_categorie = ""; //nom de la catégorie antérieur
    var compteur_ligne = 1 ; //compteur pour nombre de ligne pour chaque tableau

    $.ajax({

        url: Routing.generate('view_picture_paid_json'),

        success: function (result) {
            console.log(result);
            $.each(result, function (key, val) {

                if(val['temps'] != undefined){
                    var newDateObj = addMinutes(new Date(), val['temps']).toString().substring(16,21);
                    $("#time_new_gravure").html("<h3> Temps estimé à " + val['temps'] +" minutes, fin prévue à " + newDateObj);

                }
                else {
                    //à la première itération on recupère le nom de la catégorie
                    nom_categorie = val['name_category'];

                    //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                    if(nom_categorie != old_nom_categorie){
                        if(compteur != 0){
                            $elem += "</tbody> </table>";
                            $elem += "</div>";
                            $elem += compteur % 2 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                            compteur_ligne = 1; //remet le compteur de ligne à 1 avant de passer à la prochaine catégorie
                        }

                        $elem += compteur % 2 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                        $elem += "<div class=\"col-sm-6\" align=\"center\">";
                        $elem += "<h1><b>" + val['name_category'] + "</b></h1>"; //nom de la catégorie
                        $elem += "<table class=\"table\"> <thead> <tr> <th>Numéro</th> <th>Image</th> <th>Id Commande</th> <th>Prêt à graver</th>  <th>Date de création</th> </tr> </thead> <tbody>";

                        compteur++; //incrémentation du compteur
                    }

                    $elem += "<tr class=\"success\" style=\"background-color:lightgreen;\">";
                    $elem += "<td><h2>" + compteur_ligne + "</h2></td>";
                    $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['path-jpg'] + " \" width=\'100\'></a></td>";
                    $elem += "<td><h2>" + val['name'] + "<h2>" ;
                    if (val['name_category'] == "NoCategory") {
                        $elem += "<a class=\"btn btn-success\" id=\"add_" + val['id'] + "\" href=\"/projetZero/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a>";
                    }
                    $elem += "</td>";
                    $elem += "<td><label class=\"checkbox-inline\">" ;
                    $elem += val['check'] == 1 ? "<input type=\"checkbox\" value=\"\" checked id=\"btg_checkbox_" + val['id'] +"\" onclick=\"UncheckPicture(" + val['id'] + ");\">" : "<input type=\"checkbox\" value=\"\" id=\"btg_checkbox_" + val['id'] +"\" onclick=\"UncheckPicture(" + val['id'] + ");\">" ;
                    $elem += "</label></td>";
                    $elem += "<td>" + val['date'] + "</td>";
                    $elem += "</tr>";

                    //modal
                    $elem += "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Picture_" + val['id'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Picture_title_" + val['id'] + "\" aria-hidden=\"true\">";
                    $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
                    $elem += "<div class=\"modal-content\">";
                    $elem += "<div class=\"modal-header\">";
                    $elem += "<h5 class=\"modal-title\" id=\"Modal_Picture_title_" + val['id'] + "\"></h5>";
                    $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
                    $elem += "<span aria-hidden=\"true\">&times;</span>";
                    $elem += "</button>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-body\">";
                    $elem += "<img src=\"" + val['path-jpg'] + " \" width=\'750\'>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-footer\">";
                    $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";

                    old_nom_categorie = val['name_category'];
                    compteur_ligne++;
                }

            });
            // $elem += "<br><br>";
            $("#new_gravure").html($elem);
            $("#btn_paid_accept").addClass("btn-info").removeClass("btn-danger");  //changement de couleur du bouton
        }
    });
}


