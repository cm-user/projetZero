var array_box = []; //tableau stockant les id_prestashop des commandes
var arrayIdPrestashop = []; //tableau contenant des id prestashop des commandes qui ont des gravures sans catégorie
var number_box_max = 0; //nombre de case maximum

actualize(1); //appel de cette fonction, le paramètre 1 permet de ne pas effacer la disposition des caisses, ceci afin de prévenir une coupure internet
function buildTable() {
    $.ajax({
        url: Routing.generate('box_number_json'), //enregistre en bdd
        success: function (result) {
            var number_columns = result[0];
            var number_rows = result[1];

            number_box_max = number_columns * number_rows;
            var $elem = "<table id=\"table_order\" class=\"table-condensed\">";
            var $divDisplayCase = "";
            for (i = 0; i < number_box_max; i++) {
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
            for (y = 0; y < number_box_max; y++) {
                $divDisplayCase += "<div id=\"DisplayCase_" + (y + 1) + "\" hidden ></div>";
                array_box.push(0);
            }
            $("#div_display_case").html($divDisplayCase);
            $("#div_table").html($elem);
            $("#div_table").append("<button class=\"btn btn-default btn-block\" role=\"button\" id=\"btn_graver\" onclick='redirectionSelectionSerie();' style=\"border-radius: 15px;padding: 5%;background-color: #575354;color:white;\"><h2>GRAVER</h2></button>");
            setTimeout(function (){$('body').css('pointer-events','initial');}, 2000); //active les clics


        }
    });
}

// function cleanTable() {
//     for (y = 0; y < array_box.length; y++) {
//         array_box[y] = 0;
//         $("#case" + (y + 1)).html(""); // supprime le numéro de la caissse dans le tableau
//         $("#case" + (y + 1)).css("background-color", "#E1B7B9"); // change la couleur de la case
//         $("#DisplayCase_" + (y+1)).html(""); //suppression du numéro de commande au survol de la souris
//     }
// }

//////////au clic du bouton une requête ajax se lance pour charger les nouvelles gravures en attente////////
function actualize(bool) {
    $('body').css('pointer-events','none');//désactive les clics

    if (bool == 1) {
        buildTable(); //appel de la fonction pour construire le tableau contenant les caisses et afficher ce dernier
    }
    // else {
    //     cleanTable(); //appel de la fonction pour construire le tableau contenant les caisses et afficher ce dernier
    // }

    setTimeout(function () {
        getNumberGravures();
    }, 3000);//appel de la fonction qui va afficher le nombre de gravure et leurs états avec un délai d'attente pour laisser le temps à la bdd local de se remplir

    $("#btn_actualize").css("background-color", "#D9534F");  //changement de couleur du bouton
    $("#msg_new_gravure").html(""); //efface le contenu de la div msg
    $("#msg_hours").html("<br><h3>Actualisé à " + dateHoursMinutes() + "</h3>"); //efface le contenu de la div msg
    var $elem = "<br>";
    var compteur_order = 0;  //compteur pour calculer quand placer les rows
    var compteur_gravure = 0;  //compteur pour calculer quand placer les rows
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur
    $.ajax({

        url: Routing.generate('new_gravure_json', {bool: bool}),
        success: function (result) {
            //si le result vaut 1 cela signifie qu'une remise à zéro a été faite
            if (result == 1) {
                setTimeout(function () {
                    location.reload();
                }, 1000);//rechargement de la page
            }
            else {

                $.each(result, function (key, val) {

                    // if (val['time'] != undefined) {
                    //     var newDateObj = addMinutes(new Date(), val['time']).toString().substring(16, 21);
                    //     $("#time_new_gravure").html("<h3> Temps estimé à " + val['time'] + " minutes, fin prévue à " + newDateObj + "</h3>");
                    // }
                    if (val['msg'] != undefined) {
                        $("#msg_new_gravure").html("<br><div class=\"alert alert-danger\">Il n'y a pas de nouvelles gravures , patientez... ou pensez à augmenter le <a href='./orderregulator'>régulateur de commande</a></div>");
                    }
                    else {
                        //à la première itération on recupère le numéro de commande
                        id_order = val['id_prestashop'];

                        //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                        if (id_order != old_id_order) {
                            //si la commande est checked on ajoute au tableau son numéro de caisse
                            if (val['checked'] == 1) {
                                setTimeout(function () {
                                    addBoxArray(val['id_prestashop'], val['box']);
                                }, 2000);
                            }
                            $elem += "</tr>";
                            compteur_gravure = 0;
                            if (compteur_order != 0) {
                                $elem += "</table>";
                                $elem += "</div>";
                                $elem += compteur_order % 4 == 0 ? "</div>" : ""; //ferme la row avant d'en ouvrir une nouvelle
                            }

                            $elem += compteur_order % 4 == 0 ? "<div class=\"row\">" : ""; //affiche une row lorsque le nombre est de X
                            $elem += "<div class=\"col-sm-3\" >";

                            if (val['time_limited'] == 1) { // si la commande est tombé après l'heure limite
                                $elem += "<table class=\"table\"><tr style=\"background-color:#6C6A6B; height:97px;\">";
                            }
                            else {
                                $elem += val['state_prestashop'] == 4 ? "<table class=\"table\"> <tr style=\"background-color:#CDF3DA; height:97px;\">" : "<table class=\"table\"> <tr style=\"background-color:#FAEDDD; height:97px;\">";
                            }
                            $elem += "<td><span style=\"font-size: 45px ;text-transform: uppercase; \">";
                            $elem += id_order; //numéro de commande
                            $elem += "</span></td>";
                            // if (val['state_prestashop'] == 4) { //verifie que la commande ait le bon etat
                            $elem += "<td><label class=\"checkbox-inline\" id=\"checkbox_" + val['id_prestashop'] + "\">";
                            if (val['checked'] == 1) { //si la commande est coché
                                if (val['locked'] == 1) { // si la commande est vérrouillé on masque la croix pour supprimer cette commande
                                    $elem += "<button type='button' id=\"btg_checkbox_" + val['id_prestashop'] + "\" class='btn btn-danger' data-toggle=\"popover\" data-placement=\"bottom\" data-content=\"Cette commande a commencée la gravure, veuillez la terminer\" style='margin-left: -80px;'><h2> Case " + (val['box']) + " </h2></button>";
                                }
                                else {
                                    $elem += "<button id=\"btg_checkbox_" + val['id_prestashop'] + "\" class='btn btn-danger' style='margin-left: -80px;' onclick=\"RemoveCase(" + val['id_prestashop'] + ");\"><h2><i class=\"glyphicon glyphicon-remove-sign\"></i> Case " + (val['box']) + "</h2></button>";
                                }
                            }
                            else {
                                $elem += "<input type=\"checkbox\" value=\"\" id=\"btg_checkbox_" + val['id_prestashop'] + "\" onclick=\"addBoxArray(" + val['id_prestashop'] + ");\">";
                            }

                            $elem += "</label>";
                            $elem += "</td>";
                            // }
                            // else {
                            //     $elem += "<td></td>";
                            // }
                            $elem += "</tr>";
                            compteur_order++; //incrémentation du compteur
                        }

                        if (val['time_limited'] == 1) { // si la commande est tombé après l'heure limite
                            $elem += "<tr style=\"background-color:#6C6A6B;\">";
                        }
                        else {
                            $elem += val['state_prestashop'] == 4 ? "<tr style=\"background-color:#45DB7A;\">" : "<tr style=\"background-color:#FEAF31;\">";
                        }
                        // $elem += (val['state_prestashop'] == 4) ? "<tr  style=\"background-color:#45DB7A;\">" : "";
                        // $elem += (val['state_prestashop'] == 3) ? "<tr  style=\"background-color:#FEAF31;\">" : "";
                        $elem += "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + val['id'] + "\"><img src=\"" + val['jpg'] + " \" width='200'></a></td>";

                        if (val['id_product'] == null) {
                            $elem += addBlockEngraveWithoutProduct(val);
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
                        $elem += "<button type=\"button\" class=\"btn btn-warning\" style='float: left' data-toggle=\"modal\" data-target=\"#Modal_Engrave_Express_" + val['id'] + "\">Gravure Express</button>";
                        $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
                        $elem += "</div>";
                        $elem += "</div>";
                        $elem += "</div>";
                        $elem += "</div>";

                        //modal gravure express
                        $elem += "<div class=\"modal fade\" data-backdrop=\"static\" id=\"Modal_Engrave_Express_" + val['id'] + "\" tabindex=\"1\" role=\"dialog\" aria-hidden=\"true\">\n" +
                            "        <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">\n" +
                            "            <div class=\"modal-content\">\n" +
                            "                <div class=\"modal-body\" id=\"Modal_Engrave_Express_Body_" + val['id'] + "\">" +
                        "<p>Cette gravure sera marqué comme étant gravé et ajouté à la dernière session, confirmez-vous ?</p> " +
                        " <button class='btn btn-danger' onclick='displayModalExpress(" + val['id'] + ");'>Oui</button> " +
                        " <button class='btn btn-info' style='float: right' data-dismiss=\"modal\">Non</button> " +
                        "                </div>\n" +
                        "            </div>\n" +
                        "        </div>\n" +
                        "    </div>";

                        old_id_order = val['id_prestashop'];

                        compteur_gravure++;
                    }

                });
            }
            $("#new_gravure").html($elem);
            $("#btn_actualize").css("background-color", "#575354");  //changement de couleur du bouton
            RemoveOrderCheckBoxWithGravureWithoutCategory(arrayIdPrestashop); //suppression des checkbox si la commande contient une gravure sans catégorie

            //active les popovers
            $(function () {
                $('[data-toggle="popover"]').popover()
            })
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

function addBlockEngraveWithoutProduct(val) {
    $elem = "<td><div class=\"alert alert-danger\" role=\"alert\">Alerte cette gravure n'est pas liée à un produit type</div>" +
        "<a class=\"btn btn-warning\" id=\"add_" + val['id'] + "\" target='_blank' href=\"/gravure/product/new/" + val['id_product'] + "\" onclick=\"displayButton(" + val['id'] + ");\">Ajouter le produit type</a>" +
        "<div id=\"div_button_product_actualize_" + val['id'] + "\"></div></td>";
    arrayIdPrestashop.push(val['id_prestashop']);

    return $elem;
}

//ajout de la commande dans une boîte
function addBoxArray(id_prestashop, box) {

    if (array_box[array_box.length - 1] != 0) {
        alert("Nombre maximum de case atteint ");
    }
    else {
        if (box !== undefined) {
            array_box[box - 1] = id_prestashop;
            $("#case" + (box)).html("<a href='#' onclick=\"getAnchorByClickCase(" + id_prestashop + ");\" style='color:inherit;'>" + " " + box + "</a>"); // affiche le numéro de la caissse dans le tableau
            $("#case" + (box)).css("background-color", "#D82228"); // change la couleur de la case
            addDisplayCase(box, id_prestashop); //affiche la commande au survol du numéro de caisse dans le tableau avec les images
            addListenerMouseOver(box);

            return '';
        }

        for (i = 0; i < array_box.length; i++) {
            if (array_box[i] == 0) {
                array_box[i] = id_prestashop;
                $("#case" + (i + 1)).html("<a href='#' onclick=\"getAnchorByClickCase(" + id_prestashop + ");\" style='color:inherit;'>" + (i + 1) + "</a>"); // affiche le numéro de la caissse dans le tableau
                $("#case" + (i + 1)).css("background-color", "#D82228"); // change la couleur de la case
                addDisplayCase((i + 1), id_prestashop); //affiche la commande au survol du numéro de caisse dans le tableau avec les images
                $("#checkbox_" + id_prestashop).html("<button class='btn btn-danger' style='margin-left: -80px;' onclick=\"RemoveCase(" + id_prestashop + ");\"><h2><i class=\"glyphicon glyphicon-remove-sign\"></i> Case " + (i + 1) + "</h2></button>"); //ajout du numéro de caisse à la place de la checkbox et d'une croix pour supprimer

                ajaxAddBoxNumberCheck(id_prestashop, (i + 1));
                addListenerMouseOver(i + 1);

                return '';
            }
        }
    }

}

//enleve la commande de la boîte
function RemoveBoxArray(id_prestashop) {
    for (i = 0; i < array_box.length; i++) {
        if (array_box[i] == id_prestashop) {
            array_box[i] = 0;
            $("#case" + (i + 1)).html(""); // supprime le numéro de la caissse dans le tableau
            $("#case" + (i + 1)).css("background-color", "#E1B7B9"); // change la couleur de la case
            $("#DisplayCase_" + (i + 1)).html(""); //suppression du numéro de commande au survol de la souris

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

    var compteur = 0;

    $.ajax({
        url: Routing.generate('order_jpg_json', {id_prestashop: id_prestashop}), //enregistre en bdd
        success: function (result) {
            $elem = "<table><tr><td><span style=\"font-size: 45px ;text-transform: uppercase; \">" + id_prestashop + "</span></td></tr></table>";
            $elem += "<table>";
            $.each(result, function (key, val) {
                if (compteur % 2 == 0) {
                    $elem += "<tr><td><img src=\"" + val['jpg'] + "\" width='100'></td>";
                }
                else {
                    $elem += "<td><img src=\"" + val['jpg'] + "\" width='100'></td></tr>";
                }
                compteur++;
            });
            $elem += "</table>";
            $("#DisplayCase_" + (box)).html($elem);

        }
    });


}

function addListenerMouseOver(number) {
    $("#case" + number).mouseover(function () {
        $("#DisplayCase_" + number).show();
        $("#div_display_case").css('width', '225px');
    });
    $("#case" + number).mouseout(function () {
        $("#div_display_case").css('width', '0px');
        $("#DisplayCase_" + number).hide();
    });
}

function getAnchorBySearch(number) {
    var value = $("#recherche" + number).val();
    //vérifie que la valeur entrée permet d'afficher une ancre
    if (document.getElementById("checkbox_" + value)) {
        $('html,body').animate({scrollTop: $("#checkbox_" + value).offset().top - 100}, 'slow');
        $("#checkbox_" + number).parent().parent().fadeOut(900).delay(200).fadeIn(800);
    }
    else { //sinon cela signifie que la commande n'existe pas dans la page
        alert('Aucun résultat trouvé avec ce numéro de commande Prestashop');
    }
}

function getAnchorByClickCase(number) {
    $('html,body').animate({scrollTop: $("#checkbox_" + number).offset().top - 100}, 'slow');
    $("#checkbox_" + number).parent().parent().fadeOut(900).delay(200).fadeIn(800);
}

function goToTop() {
    $('html,body').animate({scrollTop: 0}, 'slow');
}

//fonction pour afficher le nombres de gravure à faire
function getNumberGravures() {
    $.ajax({
        url: Routing.generate('gravure_number'),
        success: function (result) {
            var expe = result['NumberExpe'];
            var soon_available = result['NumberSoonAvailable'];
            var total = result['NumberTotal'];
            var tomorrow = result['NumberTomorrow'];
            var gravure_number_fact = total + expe + soon_available;

            $("#NumberGravureExpe").html(expe);
            $("#NumberGravureSoonAvailable").html(soon_available);
            $("#NumberGravureTotalDay").html(total + "/" + gravure_number_fact);
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

                $elem += "<tr style=\"background-color:#6C6A6B;\">";
                $elem += "<td><img src=\"" + val['jpg'] + " \" width='200'></td>";

                if (val['id_product'] == null) {
                    $elem += addBlockEngraveWithoutProduct(val);
                }
                else {
                    $elem += "<td></td>"
                }


                old_id_order = val['id_prestashop'];


            });
            $("#Modal_NumberGravure_Tomorrow_Body").html($elem);
        }
    });
}

// fonction qui va afficher dans la modal les gravures qui sont en train de tomber
function getGravureSoonAvailable() {
    var $elem = "<br>";
    var compteur_order = 0;  //compteur pour calculer quand placer les rows
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur

    $("#Modal_NumberGravure_Soon_Available_Body").html("<p>Chargement en cours...</p>");

    $.ajax({
        url: Routing.generate('new_gravure_soon_available'),
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
                    $elem += "<table class=\"table\"> <tr style=\"background-color:#FAEDDD; height:97px;\">";
                    $elem += "<td><span style=\"font-size: 45px ;text-transform: uppercase; \">"; //nom de la catégorie
                    $elem += id_order; //nom de la catégorie
                    $elem += "</span></td>"; //nom de la catégorie
                    $elem += "<td></td>";
                    $elem += "</tr>";
                    compteur_order++; //incrémentation du compteur
                }

                $elem += "<tr  style=\"background-color:#FEAF31;\">";
                $elem += "<td><img src=\"" + val['jpg'] + " \" width='200'></td>";

                if (val['id_product'] == null) {
                    $elem += addBlockEngraveWithoutProduct(val);
                }
                else {
                    $elem += "<td></td>"
                }

                old_id_order = val['id_prestashop'];

            });
            $("#Modal_NumberGravure_Soon_Available_Body").html($elem);
        }
    });
}

//function qui retourne l'heure au format hh:mm
function dateHoursMinutes() {
    date = new Date();
    hours = date.getHours();
    minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
    return hours + ":" + minutes;
}

function displayButton(id) {
    $("#div_button_product_actualize_" + id).html(
        "<button class=\"btn btn-info btn-block\" onclick=\"actualize(1);\" role=\"button\"\n" +
        "                    <h5>Actualiser</h5>\n" +
        "                </button>"
    );
}

function redirectionSelectionSerie() {
    window.location.replace('http://localhost/projetZero2/web/app_dev.php/gravure/serie/');
}

//si l'utilisateur presse la touche entrée dans le champ recherche
$(".form-control").keypress(function (e) {
    if (e.which == 13) {
        number = this.id.substr(-1, 1);
        getAnchorBySearch(number);//appel de la fonction pour déclencher l'ancre
    }
});

// //fonction pour actualiser et effacer la session en cours
// function ActualizeAndClean() {
//     actualize(); //appel de la fonction pour actualiser
//     $("#new_gravure").html(""); //efface les nouvelles gravures
// }


function displayModalExpress(id) {
    $('#Modal_Picture_' + id).modal('hide');
    $('#Modal_Engrave_Express_' + id).modal('show');
    $('#Modal_Engrave_Express_Body_' + id).html('...');
    var $elem = "<p>Cette gravure est maintenant considérée comme gravé</p><table class='table'>";
    $.ajax({
        url: Routing.generate('gravure_mono_text', {id: id}),
        success: function (result) {
            $.each(result, function (key, val) {
                $elem += "<tr>" +
                    "<td>" + val['name_block'] + "</td>" +
                    "<td>" + val['value'] + "</td>" +
                    "</tr>"
            });
            $elem += "</table>";
            $elem += "<a href=\"/projetZero2/web/app_dev.php/gravure/session/download-pdf/" + id + "\"><button class=\"btn btn-warning\">Télécharger le PDF</button></a>";
            $elem += "<button type=\"button\" onclick='actualize(1);' class=\"btn btn-secondary\" data-dismiss=\"modal\" style='float: right;'>Fermer</button>";
            $('#Modal_Engrave_Express_Body_' + id).html($elem);

        },
        error: function (result) {
            console.log("erreur au chargement des textes pour la gravure express");
            alert("Erreur au chargement des textes pour la gravure express.");
        }
    });
}