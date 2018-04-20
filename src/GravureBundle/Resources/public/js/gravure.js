//masque les boutons download
// $("#btn_download").hide();
// $("#btn_download2").hide();

var array_box = []; //tableau stockant les id_prestashop des commandes
var arrayIdPrestashop = []; //tableau contenant des id prestashop des commandes qui ont des gravures sans catégorie

actualize(1); //appel de cette fonction, le paramètre 1 permet de ne pas effacer la disposition des caisses, ceci afin de prévenir une coupure internet

function buildTable() {

    $.ajax({
        url: Routing.generate('box_number_json'), //enregistre en bdd
        success: function (result) {
            var number_columns = result[0];
            var number_rows = result[1];

            var number_box = number_columns * number_rows;
            var $elem = "<table id=\"table_order\" class=\"table-condensed\">";
            var $divDisplayCase = "";
            for (i = 0; i < number_box; i++) {
                if (i == 0) {
                    $elem += "<tr><td id=\"case" + (i + 1) + "\"></td>";
                }
                else {
                    if (i % number_columns == 0) {
                        $elem += "</tr><tr><td id=\"case" + (i + 1) + "\"></td>";
                    }
                    else {
                        $elem += "<td id=\"case" + (i + 1) + "\"></td>";
                    }
                }
            }
            for (y = 0; y < number_box; y++) {
                $divDisplayCase += "<div id=\"DisplayCase_" + (y+1) + "\" hidden ></div>";
                array_box.push(0);
            }
            $("#div_display_case").html($divDisplayCase);
            $("#div_table").html($elem);
            $("#div_table").append("<button class=\"btn btn-default btn-block\" role=\"button\" id=\"btn_graver\" style=\"border-radius: 15px;padding: 5%;background-color: #575354;color:white;\"><h2>GRAVER</h2></button>");
            // $("#div_table").append("<br><br><br><br><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Button_Reset\"><button class=\"btn btn-default\" role=\"button\" style=\"border-radius: 15px;padding: 1%;background-color: #D82228;color:white;\"><h4>Remise à zéro</h4></button></a>");

            //modal confirmation remise à zéro
            // $elem = "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Button_Reset\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Button_Reset_Title\" aria-hidden=\"true\">";
            // $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
            // $elem += "<div class=\"modal-content\">";
            // $elem += "<div class=\"modal-header\">";
            // $elem += "<h5 class=\"modal-title\" id=\"Modal_Button_Reset_Title\"></h5>";
            // $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
            // $elem += "<span aria-hidden=\"true\">&times;</span>";
            // $elem += "</button>";
            // $elem += "</div>";
            // $elem += "<div class=\"modal-body\">";
            // $elem += "<p>Etes-vous sur de vouloir supprimer la sélection des caisses en cours ?</p>";
            // $elem += "</div>";
            // $elem += "<div class=\"modal-footer\">";
            // $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\" onclick='actualize(0)'>Oui</button>";
            // // $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Non</button>";
            // $elem += "</div>";
            // $elem += "</div>";
            // $elem += "</div>";
            // $elem += "</div>";

            // $("#div_table").append($elem);


        }
    });
}

function cleanTable() {
    for (y = 0; y < array_box.length; y++) {
        array_box[y] = 0;
        $("#case" + (y + 1)).html(""); // supprime le numéro de la caissse dans le tableau
        $("#case" + (y + 1)).css("background-color", "#E1B7B9"); // change la couleur de la case
        $("#DisplayCase_" + (y+1)).html(""); //suppression du numéro de commande au survol de la souris
    }
}

//////////au clic du bouton une requête ajax se lance pour charger les nouvelles gravures en attente////////
function actualize(bool) {

    if(bool == 1){
        buildTable(); //appel de la fonction pour construire le tableau contenant les caisses et afficher ce dernier
    }
    else {
        cleanTable(); //appel de la fonction pour construire le tableau contenant les caisses et afficher ce dernier
    }

    getNumberGravures(); //appel de la fonction qui va afficher le nombre de gravure et leurs états

    $("#btn_actualize").css("background-color", "#D9534F");  //changement de couleur du bouton
    $("#msg_new_gravure").html(""); //efface le contenu de la div msg
    $("#msg_hours").html("<br><h3>Actualiser à " + dateHoursMinutes() + "</h3>"); //efface le contenu de la div msg
    var $elem = "<br>";
    var compteur_order = 0;  //compteur pour calculer quand placer les rows
    var compteur_gravure = 0;  //compteur pour calculer quand placer les rows
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur
    $.ajax({

        url: Routing.generate('new_gravure_json', {bool: bool}),

        success: function (result) {
            console.log(result);
            $.each(result, function (key, val) {

                if (val['time'] != undefined) {
                    var newDateObj = addMinutes(new Date(), val['time']).toString().substring(16, 21);
                    $("#time_new_gravure").html("<h3> Temps estimé à " + val['time'] + " minutes, fin prévue à " + newDateObj + "</h3>");
                }
                else if (val['msg'] != undefined) {
                    $("#msg_new_gravure").html("<br><div class=\"alert alert-danger\">Il n'y a pas de nouvelles gravures , patientez... ou pensez à augmenter le <a href='./orderregulator'>régulateur de commande</a></div>");
                }
                else {
                    //à la première itération on recupère le numéro de commande
                    id_order = val['id_prestashop'];

                    //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                    if (id_order != old_id_order) {
                        //si la commande est checked on ajoute au tableau son numéro de caisse
                        if (val['checked'] == 1) {
                            setTimeout(function(){ addBoxArray(val['id_prestashop'], val['box']); },2000);
                        }
                        $elem += "</tr>";
                        compteur_gravure = 0;
                        if (compteur_order != 0) {
                            $elem += "</table>";
                            $elem += "</div>";
                            $elem += compteur_order % 3 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                        }

                        $elem += compteur_order % 3 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                        $elem += "<div class=\"col-sm-4\" >";
                        $elem += val['state_prestashop'] == 4 ? "<table class=\"table\"> <tr style=\"background-color:#CDF3DA; height:97px;\">" : "<table class=\"table\"> <tr style=\"background-color:#FAEDDD; height:97px;\">";
                        $elem += "<td><span style=\"font-size: 45px ;text-transform: uppercase; \">"; //nom de la catégorie
                        $elem += id_order; //nom de la catégorie
                        $elem += "</span></td>"; //nom de la catégorie
                        if (val['state_prestashop'] == 4) { //verifie que la commande ait le bon etat
                            $elem += "<td><label class=\"checkbox-inline\" id=\"checkbox_" + val['id_prestashop'] + "\">";
                            $elem += val['checked'] == 1 ? "<button id=\"btg_checkbox_" + val['id_prestashop'] + "\" class='btn btn-danger' onclick=\"RemoveCase(" + val['id_prestashop'] + ");\"><h2><i class=\"glyphicon glyphicon-remove-sign\"></i> Case " + (val['box']) + "</h2></button>" : "<input type=\"checkbox\" value=\"\" id=\"btg_checkbox_" + val['id_prestashop'] + "\" onclick=\"addBoxArray(" + val['id_prestashop']  + ");\">";
                            $elem += "</label>";
                            $elem += "</td>";
                        }
                        else {
                            $elem += "<td></td>";
                        }
                        // $elem += "<td></td>";
                        $elem += "</tr>";
                        compteur_order++; //incrémentation du compteur
                    }

                    $elem += (val['state_prestashop'] == 4) ? "<tr  style=\"background-color:#45DB7A;\">" : "";
                    $elem += (val['state_prestashop'] == 3) ? "<tr  style=\"background-color:#FEAF31;\">" : "";
                    $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['jpg'] + " \" width='100'></a></td>";

                    if (val['id_product'] == null) {
                        $elem += "<td><div class=\"alert alert-danger\" role=\"alert\">Alerte ce produit n'a pas de catégorie</div><a class=\"btn btn-warning\" id=\"add_" + val['id'] + "\" href=\"/projetZero2/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a></td>";
                        arrayIdPrestashop.push(val['id_prestashop']);
                    }
                    else if (val['state_prestashop'] == 3) {
                        $elem += "<td><div class=\"alert alert-warning\" role=\"alert\">En cours de préparation</div></td>";
                    }
                    else {
                        $elem += "<td></td>"
                    }

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
                    $elem += "<img src=\"" + val['jpg'] + " \" width=\'750\'>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-footer\">";
                    $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";

                    old_id_order = val['id_prestashop'];

                    compteur_gravure++;
                }


            });
            // $elem += "<br><br>";
            $("#new_gravure").html($elem);
            $("#btn_actualize").css("background-color", "#575354");  //changement de couleur du bouton
            RemoveOrderCheckBoxWithGravureWithoutCategory(arrayIdPrestashop); //suppression des checkbox si la commande contient une gravure sans catégorie
        }
    });
}

//suppression des checkbox si la commande contient une gravure sans catégorie
function RemoveOrderCheckBoxWithGravureWithoutCategory(arrayIdPrestashop) {
    for (i = 0; i < arrayIdPrestashop.length; i++) {
        $("#checkbox_" + arrayIdPrestashop[i]).html(""); //efface la checkbox si la commande contient une gravure sans catégorie
    }
}

//fonction pour ajouter des minutes à un objet date
function addMinutes(date, minutes) {
    return new Date(date.getTime() + minutes * 60000);
}

//fonction pour uncheck la gravure afin qu'elle ne soit pas prise en compte au lancement d'une nouvelle session
function UncheckPicture(id_prestashop) {
    $.ajax({
        url: Routing.generate('order_uncheck', {id_prestashop: id_prestashop}), //enregistre en bdd
        success: function (result) {
            console.log(result);
        }
    });
}

//ajout de la commande dans une boîte
function addBoxArray(id_prestashop, box) {

    if (box !== undefined) {
        array_box[box - 1] = id_prestashop;
        $("#case" + (box)).html("<a href=\"#checkbox_" + id_prestashop + "\" style='color:inherit;'>" + " " + box + "</a>"); // affiche le numéro de la caissse dans le tableau
        $("#case" + (box)).css("background-color", "#D82228"); // change la couleur de la case
        // $("#DisplayCase_" + (box)).html("<td><span style=\"font-size: 45px ;text-transform: uppercase; \">" + id_prestashop + "</span></td>");
        addDisplayCase(box, id_prestashop); //affiche la commande au survol du numéro de caisse dans le tableau avec les images
        addListenerMouseOver(box);

        return '';
    }

    for (i = 0; i < array_box.length; i++) {
        if (array_box[i] == 0) {
            array_box[i] = id_prestashop;
            console.log(array_box);
            $("#case" + (i + 1)).html("<a href=\"#checkbox_" + id_prestashop + "\" style='color:inherit;'>" + (i + 1) + "</a>"); // affiche le numéro de la caissse dans le tableau
            $("#case" + (i + 1)).css("background-color", "#D82228"); // change la couleur de la case
            // $("#DisplayCase_" + (i+1)).html("<td><span style=\"font-size: 45px ;text-transform: uppercase; \">" + id_prestashop + "</span></td>"); //affiche la commande au survol du numéro de caisse dans le tableau
            addDisplayCase((i + 1), id_prestashop); //affiche la commande au survol du numéro de caisse dans le tableau avec les images
            $("#checkbox_" + id_prestashop).html("<button class='btn btn-danger' onclick=\"RemoveCase(" + id_prestashop + ");\"><h2><i class=\"glyphicon glyphicon-remove-sign\"></i> Case " + (i + 1) + "</h2></button>"); //ajout du numéro de caisse à la place de la checkbox et d'une croix pour supprimer

            ajaxAddBoxNumberCheck(id_prestashop, (i + 1));
            addListenerMouseOver(i+1);

            return '';
        }
    }
}

//enleve la commande de la boîte
function RemoveBoxArray(id_prestashop) {
    for (i = 0; i < array_box.length; i++) {
        if (array_box[i] == id_prestashop) {
            array_box[i] = 0;
            console.log(array_box);
            $("#case" + (i + 1)).html(""); // supprime le numéro de la caissse dans le tableau
            $("#case" + (i + 1)).css("background-color", "#E1B7B9"); // change la couleur de la case
            $("#DisplayCase_" + (i+1)).html(""); //suppression du numéro de commande au survol de la souris


            ajaxAddBoxNumberCheck(id_prestashop, (i + 1));

            return '';
        }
    }
}

function RemoveCase(id_prestashop) {
    RemoveBoxArray(id_prestashop);
    $("#checkbox_" + id_prestashop).html("<input type=\"checkbox\" id=\"btg_checkbox_" + id_prestashop + "\" onclick=\"addBoxArray(" + id_prestashop + ");\">");
}

function ajaxAddBoxNumberCheck(id_prestashop, box) {
    $.ajax({
        url: Routing.generate('order_add_box', {id_prestashop: id_prestashop, box: box}), //enregistre en bdd
        success: function (result) {
        }
    });
}

//affiche la commande au survol du numéro de caisse dans le tableau avec ces images
function addDisplayCase(box, id_prestashop) {

    console.log("numéro de box :" + box);
    var compteur = 0;

    $.ajax({
        url: Routing.generate('order_jpg_json', {id_prestashop: id_prestashop}), //enregistre en bdd
        success: function (result) {
            $elem = "<table><tr><td><span style=\"font-size: 45px ;text-transform: uppercase; \">" + id_prestashop + "</span></td></tr></table>";
            $elem += "<table>";
            $.each(result, function (key, val) {
                if(compteur % 2 == 0) {
                    $elem += "<tr><td><img src=\""+ val['jpg'] + "\" width='100'></td>";
                }
                else {
                    $elem += "<td><img src=\""+ val['jpg'] + "\" width='100'></td></tr>";
                }
                compteur ++;
                console.log("valeur du compteui: " + compteur);
            });
            $elem += "</table>";
            console.log($elem);
            $("#DisplayCase_" + (box)).html($elem);

        }
    });


}

function addListenerMouseOver(number) {
    $("#case" + number).mouseover(function(){
        $("#DisplayCase_" + number).show();
        $("#div_display_case").css('width', '225px');
    });
    $("#case" + number).mouseout(function(){
        $("#div_display_case").css('width', '0px');
        $("#DisplayCase_" + number).hide();
    });
}

function getAnchorBySearch(){
    var value = $("#recherche").val();
    $('html,body').animate({scrollTop: $("#checkbox_"+ value).offset().top}, 'slow');
}

// $("#recherche").submit(function(event) {
//     event.preventDefault();
//     console.log(event);
// });

//fonction pour afficher le nombres de gravure à faire
function getNumberGravures() {
    $.ajax({
        url: Routing.generate('gravure_number'),
        success: function (result) {
            var expe = result['NumberExpe'];
            var prepa = result['NumberPrepa'];
            var today = result['NumberToday'];
            var tomorrow = result['NumberTomorrow'];

            $("#NumberGravureExpe").html(expe);
            $("#NumberGravurePrepa").html(prepa);
            $("#NumberGravureTotalDay").html(today);
            $("#NumberGravureTomorrow").html(tomorrow);
        }
    });
}

// fonction qui va afficher dans la modal les gravures qui seront à faire après l'heure limite
function getGravureTomorrow() {
    $elem = "";

    var $elem = "<br>";
    var compteur_order = 0;  //compteur pour calculer quand placer les rows
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur

    $("#Modal_NumberGravure_Tomorrow_Body").html("<p>Chargement en cours...</p>");

    $.ajax({

        url: Routing.generate('new_gravure_tomorrow'),

        success: function (result) {
            $.each(result, function (key, val) {

                    //à la première itération on recupère le numéro de commande
                    id_order = val['id_prestashop'];

                    //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                    if (id_order != old_id_order) {

                        $elem += "</tr>";
                        if (compteur_order != 0) {
                            $elem += "</table>";
                            $elem += "</div>";
                            $elem += compteur_order % 2 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                        }

                        $elem += compteur_order % 2 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                        $elem += "<div class=\"col-sm-6\" >";
                        $elem += "<table class=\"table\"> <tr style=\"background-color:#6C6A6B; height:97px;\">";
                        $elem += "<td><span style=\"font-size: 45px ;text-transform: uppercase; \">"; //nom de la catégorie
                        $elem += id_order; //nom de la catégorie
                        $elem += "</span></td>"; //nom de la catégorie
                        $elem += "<td></td>";
                        $elem += "</tr>";
                        compteur_order++; //incrémentation du compteur
                    }

                    $elem +=  "<tr  style=\"background-color:#6C6A6B;\">" ;
                    $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['jpg'] + " \" width='100'></a></td>";

                    if (val['id_product'] == null) {
                        $elem += "<td><div class=\"alert alert-danger\" role=\"alert\">Alerte ce produit n'a pas de catégorie</div><a class=\"btn btn-warning\" id=\"add_" + val['id'] + "\" href=\"/projetZero2/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a></td>";
                        arrayIdPrestashop.push(val['id_prestashop']);
                    }
                    else {
                        $elem += "<td></td>"
                    }

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
                    $elem += "<img src=\"" + val['jpg'] + " \" width=\'750\'>";
                    $elem += "</div>";
                    $elem += "<div class=\"modal-footer\">";
                    $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";
                    $elem += "</div>";

                    old_id_order = val['id_prestashop'];


            });
            $("#Modal_NumberGravure_Tomorrow_Body").html($elem);
        }
    });
}

// fonction qui va afficher dans la modal les gravures qui seront à faire avant l'heure limite
function getGravureToday() {
    $elem = "";

    var $elem = "<br>";
    var compteur_order = 0;  //compteur pour calculer quand placer les rows
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur

    $("#Modal_NumberGravure_Today_Body").html("<p>Chargement en cours...</p>");

    $.ajax({

        url: Routing.generate('new_gravure_today'),

        success: function (result) {
            $.each(result, function (key, val) {

                //à la première itération on recupère le numéro de commande
                id_order = val['id_prestashop'];

                //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                if (id_order != old_id_order) {

                    $elem += "</tr>";
                    if (compteur_order != 0) {
                        $elem += "</table>";
                        $elem += "</div>";
                        $elem += compteur_order % 2 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                    }

                    $elem += compteur_order % 2 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                    $elem += "<div class=\"col-sm-6\" >";
                    $elem += "<table class=\"table\"> <tr style=\"background-color:#6C6A6B; height:97px;\">";
                    $elem += "<td><span style=\"font-size: 45px ;text-transform: uppercase; \">"; //nom de la catégorie
                    $elem += id_order; //nom de la catégorie
                    $elem += "</span></td>"; //nom de la catégorie
                    $elem += "<td></td>";
                    $elem += "</tr>";
                    compteur_order++; //incrémentation du compteur
                }

                $elem +=  "<tr  style=\"background-color:#6C6A6B;\">" ;
                $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['jpg'] + " \" width='100'></a></td>";

                if (val['id_product'] == null) {
                    $elem += "<td><div class=\"alert alert-danger\" role=\"alert\">Alerte ce produit n'a pas de catégorie</div><a class=\"btn btn-warning\" id=\"add_" + val['id'] + "\" href=\"/projetZero2/web/app_dev.php/engraving/category/new/" + val['id_product'] + "\">Ajouter cette catégorie</a></td>";
                    arrayIdPrestashop.push(val['id_prestashop']);
                }
                else {
                    $elem += "<td></td>"
                }

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
                $elem += "<img src=\"" + val['jpg'] + " \" width=\'750\'>";
                $elem += "</div>";
                $elem += "<div class=\"modal-footer\">";
                $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                $elem += "</div>";
                $elem += "</div>";
                $elem += "</div>";
                $elem += "</div>";

                old_id_order = val['id_prestashop'];


            });
            $("#Modal_NumberGravure_Today_Body").html($elem);
        }
    });
}

//function qui retourne l'heure au format hh:mm
function dateHoursMinutes() {
    date = new Date();
    hours = date.getHours();
    minutes = date.getMinutes() < 10 ? "0" +  date.getMinutes() :  date.getMinutes() ;
    return hours + ":" + minutes ;
}

//fonction pour actualiser et effacer la session en cours
function ActualizeAndClean() {
    actualize(); //appel de la fonction pour actualiser
    $("#new_gravure").html(""); //efface les nouvelles gravures
    // $("#ongoing_gravure").html(""); //efface le contenu de la div
}
