var array_gravure_temp = [];
const EN_COURS = 3;
const TERMINE = 5;
buildTable(); //construit le tableau pour afficher les caisses
createChainSession(); // construit la chaîne de série de gravure de la session en cours
setTimeout(function () {
    setColorBlackForCaseFull();
}, 4000);//remet les cases non vide à la même couleur

function createChainSession() {
    $.ajax({
        url: Routing.generate('assistant_gravure_chain'),
        success: function (result) {

            if(result.length == 0){ //si l'utilisateur a gravé toutes les chaînes, la session se termine alors automatiquement
                setNumberEngraveInSession(); //ajout du nombre total de gravure de la session qui est alors terminé
                $("#Modal_Session_Finish").modal('show');
            }
            else{
                $elem = "<table><thead style='background-color: #EFEFEF;'><td>Nb</td><td>Produits</td><td>Statut</td></thead><tbody>";
                $elem2 = "";
                $.each(result, function (key, val) {
                    $elem += "<tr style=\"background-color: " + val['color'] + ";\" id=\"chain_number_" + (key + 1) + "\">";
                    $elem += "<td>" + val['gravures'].length + "</td>";
                    $elem += "<td><a style=\"display:block;width:100%;height:100%; cursor: pointer;\" onclick=\"addListenerChangeColorCase(" + (key + 1) + ",'" + val['color'] + "');\">" + val['surname'] + "</a></td>";
                    if (val['status'] == EN_COURS) {
                        $elem += "<td id=\"case_status_" + val['chain_number'] + "\" data-toggle=\"modal\" data-target=\"#Modal_Serie_Gravure_" + val['chain_number'] + "\" style='cursor: pointer'> En Cours...</td>";
                    }
                    else if (val['status'] == TERMINE) {
                        $elem += "<td id=\"case_status_" + val['chain_number'] + "\">Terminé</td>";
                    }
                    else {
                        $elem += "<td id=\"case_status_" + val['chain_number'] + "\"></td>";
                    }
                    $elem += "</tr>";

                    //vérifie le type de la machine pour la chaîne afin d'afficher une modal plus pertinente
                    if (val['machine'] == 'mail') {
                        $elem2 += addModalForMachineMail(val);
                    }
                    else if (val['machine'] == 'pdf') {
                        $elem2 += addModalForMachinePdf(val);
                    }
                    else {
                        alert("une gravure n'a pas de machine liée");
                    }
                });

                $elem += "</tbody></table>";

                $("#div_chain_category").html($elem); //affiche les différentes chaînes
                $("#div_modal_list_gravure").html($elem2); //rentre des modals dans la div
            }

        },
        error: function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });

}

function buildTable() {

    $.ajax({
        url: Routing.generate('box_number_json'),
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
                $divDisplayCase += "<div id=\"DisplayCase_" + (y + 1) + "\" hidden ></div>";
            }
            $("#div_display_gravure").html($divDisplayCase);
            $("#div_table").html($elem);

        },
        error: function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
    setTimeout(function () {
        hydrateTable();
    }, 2000); //remplit le tableau avec les numéros de caisses
}


//remplit le tableau avec les numéros de caisses
function hydrateTable() {
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur
    var array_gravure = [];
    $.ajax({
        url: Routing.generate('gravure_chain_number_json'),
        success: function (result) {
            console.log(result);
            $.each(result, function (key, val) {
                //à la première itération on recupère le numéro de commande
                id_order = val['id_prestashop'];

                //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                if (id_order != old_id_order && old_id_order != "") {
                    addListenerCase(array_gravure); //ajout du numéro de caisse au tableau
                    array_gravure = []; //vide le tableau
                }
                 if (result.length - 1 == key) {
                    array_gravure.push({
                        'jpg': val['jpg'],
                        'colorGravure': val['colorGravure'],
                        'colorCategory': val['colorCategory'],
                        'id': val['id'],
                        'chain_number': val['chain_number'],
                        'box': val['box'],
                        'id_prestashop': val['id_prestashop'],
                        'alias': val['alias']
                    }); //ajout dans le tableau les id des gravures
                    addListenerCase(array_gravure); //ajout du numéro de caisse au tableau
                    array_gravure = []; //vide le tableau
                }

                array_gravure.push({
                    'jpg': val['jpg'],
                    'colorGravure': val['colorGravure'],
                    'colorCategory': val['colorCategory'],
                    'id': val['id'],
                    'chain_number': val['chain_number'],
                    'box': val['box'],
                    'id_prestashop': val['id_prestashop'],
                    'alias': val['alias']
                }); //ajout dans le tableau les id des gravures
                old_id_order = val['id_prestashop'];

            });
        },
        error: function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

//ajout d'une classe aux différentes cases en fonction du numéro de la ou les chaînes liées aux gravures contenue dans les caisses
function addListenerCase(array_gravure) {
    var numberBox = array_gravure[0]['box'];
    $elem = "<div class='row' style='margin-left: 30px;'><table><thead><th style='font-size:50px;background-color: #EFE7E7;'>" + array_gravure[0]['id_prestashop'] + "</th></thead><tbody>";

    $("#case" + numberBox).html(numberBox); //affiche le numéro de caisse
    for (i = 0; i < array_gravure.length; i++) {
        $("#case" + numberBox).addClass("chain_" + array_gravure[i]['chain_number']); //ajout d'une classe avec le numéro de chaine
        if (array_gravure[i]['colorCategory'] !== "") {
            $elem += "<tr style=\"background-color:" + array_gravure[i]['colorCategory'] + ";\"><td style='padding: 3%; width:200px;'><img src=\"" + array_gravure[i]['jpg'] + "\" width='180'>";
            $elem += "<h4>" + array_gravure[i]['alias'] + "</h4></div></td>";
            $elem += "</tr>";
        }
        else if (array_gravure[i]['colorGravure'] !== "") {
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + array_gravure[i]['colorGravure'] + ";\"><td style='padding: 3%; width:200px;'><img src=\"" + array_gravure[i]['jpg'] + "\" width='180'>";
            $elem += "<h4>" + array_gravure[i]['alias'] + "</h4></div></td>";
            $elem += "</tr>";
        }
        else {
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + color_machine + ";\"><td style='padding: 3%; width:200px;'><img src=\"" + array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "</tr>";
        }
    }

    $elem += "</tbody></table></div>";
    $("#DisplayCase_" + numberBox).html($elem);

    addListenerClic(numberBox); //ajout d'un listener au click affiche les images

}

//ajout d'un listener au click affiche les images
function addListenerClic(number) {
    $("#case" + number).click(function () {
        var array_number_case = []; //tableau contenant les numéros de cases lié à la chaîne

        $("#div_display_gravure > div").hide(); // cache toutes les images

        if ($("#case" + number).css("background-color") !== "rgb(0, 0, 0)") {
            //parcours de toutes les cases pour récupérer uniquement les cases liées à la chaîne
            $("#table_order td").each(function (i) {
                if (this.style.backgroundColor !== "black" && this.style.backgroundColor !== "") { //vérifie que la couleur ne soit ni noir ni celle par défaut
                    array_number_case.push((i + 1));
                }
            });
            setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
            //met les case liées à la chaîne à la même couleur que cette dernière
            array_number_case.forEach(function (element) {
                $("#case" + element).css("background-color", color_chain); //change la couleur des cases par celle de la chaîne
            });

            array_number_case = []; // vide le tableau
        }
        else {
            $("#div_chain_category tbody tr").css("opacity", "1"); //remet l'opacité à 1 pour toutes les lignes des chaînes
            setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
            $("#div_button_gravure").html(""); //masque le bouton Gravure si la case n'appartient pas à la chaîne mise en évidence
        }
        $("#case" + number).css("opacity", "0.5"); //change uniquement la couleur de la caisse
        $("#DisplayCase_" + number).show(); //affiche les gravures de la case
    });
}

//Change la couleur des cases en fonction de la machine au clic sur une catégorie
function addListenerChangeColorCase(number, color) {
    $("#div_chain_category tbody tr").css("opacity", "0.5"); //baisse l'opacité de toutes les lignes des chaînes
    $("#chain_number_" + number).css("opacity", "1"); //augmente l'opacité de la ligne cliqué
    color_chain = color; // ajout de la couleur de la chaîne cliqué dans la variable color_chain

    $("#div_display_gravure > div").hide(); //masque les gravures
    setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
    $(".chain_" + number).css("background-color", color); //change de couleur les cases contenant une gravure de la chaîne

    var case_number = $(".chain_" + number).length;//nombre de case liée à la chaîne cliqué
    if (case_number == 1) { //si il n'y a qu'une seule case on affiche le contenu lié
        var case_digit = $(".chain_" + number).attr('id').replace("case", ""); //numéro de la case
        $("#DisplayCase_" + case_digit).show(); //affiche les gravures de la case
    }

    $(".chain_" + number).css("opacity", "1"); //remet l'opacité pour ces cases à 1

    //affiche la modal avec la liste des gravures et le gabarit lié à la série au clic du bouton Gravure
    $("#div_button_gravure").html("<br><br><button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#Modal_Serie_Gravure_" + number + "\" role=\"button\"\n" +
        "                        style=\"border-radius: 15px;padding: 10px;background-color: #575354;color:white;\">\n" +
        "                    <h3>\n" +
        "                        <i class=\"glyphicon glyphicon-download-alt\" style=\"font-size:35px;\"></i>\n" +
        "                        Gravure\n" +
        "                    </h3>\n" +
        "                </button>");

}

//met en noir les cases contenant les numéros
function setColorBlackForCaseFull() {
    $("#table_order td").each(function (i) {
        if ($(this).html() != "") { //vérifie que la cellule contienne bien un numéro
            $(this).css("background-color", "black"); //change le fond en noir
            $(this).css("opacity", "1"); //remet l'opacité à 1
        }
    });
}

//change le statut en COURS pour les gravures liées à cette chaîne
function setStatusGravureOnLoad(chain_number) {
    $.ajax({
        url: Routing.generate('gravure_status_on_load', {chainNumber: chain_number}),
        success: function (result) {

            $("#Modal_Serie_Gravure_" + chain_number).modal('hide'); //ferme la modal
            $("#case_status_" + chain_number).html("En Cours...");
            //ajout de ces attributs pour pouvoir ouvrir la modal en cliquant sur En Cours dans le tableau des chaînes
            $("#case_status_" + chain_number).attr('data-toggle', 'modal');
            $("#case_status_" + chain_number).attr('data-target', '#Modal_Serie_Gravure_' + chain_number);
            $("#case_status_" + chain_number).attr('style', 'cursor: pointer');

            $elem = "";
            $elem += "<h3>Avez-vous bien rangé les produits ?</h3> " +
                "<button class='btn btn-success' onclick=\"setStatusGravureFinish(" + chain_number + ");\"><h3> J'ai rangé les produits</h3></button>" +
                "<button class='btn btn-danger' onclick=\"closeModalSerieGravure(" + chain_number + "); \"> " +
                "<h3>Retour </h3></button> " +
                "</div>";

            $("#div_msg_down_modal_" + chain_number).html($elem);
        },
        error: function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

//change le statut en TERMINE pour les gravures et fait disparaître la chaîne du tableau
function setStatusGravureFinish(chain_number) {
    $.ajax({
        url: Routing.generate('gravure_status_finish', {chainNumber: chain_number}),
        success: function (result) {

            if(result[0] != "" || result[1] != ""){
                $elem = "<table class='table'><tr><td><h3>Des commandes sont prêtes :</h3></td><td></td></tr>";
                $elem += result[0] != "" ? "<tr><td><h4><i class='glyphicon glyphicon-envelope'></i>&nbsp;&nbsp;" + result[0] + "</h4></td><td><input onclick=\"isChecked(" + chain_number + ");\" type=\"checkbox\" id=\"Checkbox_Engrave_Expe\"></td></tr>" : "<tr><td><h4></h4></td><td><input type=\"checkbox\" id=\"Checkbox_Engrave_Expe\" checked hidden></td></tr>";
                $elem +=  result[1] != "" ? "<tr><td><h4><i class='glyphicon glyphicon-gift'></i>&nbsp;&nbsp;" + result[1] + "</h4></td><td><input onclick=\"isChecked(" + chain_number + ");\" type=\"checkbox\" id=\"Checkbox_Engrave_Gift\"></td></tr></table>" : "<tr><td><h4></h4></td><td><input type=\"checkbox\" id=\"Checkbox_Engrave_Gift\" checked hidden></td></tr></table>" ;
                $elem += "<button class='btn btn-danger' onclick=\"cancelFinishEngrave(" + chain_number + ");\">Retour</button>";

                $("#Modal_Engrave_Finish_Body").html($elem);
                $('#Modal_Engrave_Finish').modal({backdrop: 'static', keyboard: false});
                $("#Modal_Engrave_Finish").modal('show');

            }
            else {
                buildTable(); //construit le tableau pour afficher les caisses
                createChainSession(); // construit la chaîne de série de gravure de la session en cours
                setTimeout(function () {
                    setColorBlackForCaseFull();
                }, 4000);//remet les cases non vide à la même couleur

                $("#Modal_Serie_Gravure_" + chain_number).modal('hide'); //ferme la modal
            }

        },
        error: function (result) {
            console.log("erreur au changement de statut en terminé");
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

function closeModalSerieGravure(chain_number) {
    $('#Modal_Serie_Gravure_' + chain_number).modal('hide');
}

//Ajout de la modal spécifique aux gravures avec pdf
function addModalForMachinePdf(val) {

    $elem2 = "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Serie_Gravure_" + val['chain_number'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Serie_Gravure_Title_" + val['chain_number'] + "\" aria-hidden=\"true\">";
    $elem2 += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
    $elem2 += "<div class=\"modal-content\">";
    $elem2 += "<div class=\"modal-header\">";
    $elem2 += "<h3 class=\"modal-title\" id=\"Modal_Serie_Gravure_Title_" + val['chain_number'] + "\" style=\"color:" + val['color'] + "\"> Série " + val['surname'] + "s  </h3>";
    $elem2 += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
    $elem2 += "<i class='glyphicon glyphicon-remove'></i>";
    $elem2 += "</button>";
    $elem2 += "</div>";
    $elem2 += "<div class=\"modal-body\">";

    $elem2 += "<div class='row'>";
    $elem2 += "<div class=\"col-sm-3\">" +
        "<img src=\"" + val['path_gabarit'] + "\" style='max-height: 300px; max-width: 300px' >" +
        "<h3>Gabarit : " + val['name_gabarit'] + "</h3>" +
        "<p>Positionnez les produits suivants sur ce gabarit dans le bon ordre.</p>" +
        "</div>";

    for (var i = 0; i < val['gravures'].length; i++) {
        $elem2 += "<div class=\"col-sm-3\" align='center'>";
        $elem2 += "<h3 style='background-color:black; color:white; text-align: center; margin: 0px 90px;'>Position " + (i + 1) + "</h3>";
        $elem2 += "<img src=\"" + val['gravures'][i]['path_jpg'] + "\" style='max-height: 300px; max-width: 300px' >";
        $elem2 += "<i><h4 style='text-align: center;'>" + val['gravures'][i]['alias'] + "</h4></i>";

        $elem2 += "<button class=\"btn btn-primary\" type=\"button\" data-toggle=\"collapse\" " +
            "data-target=\"#collapse_text_" + val['gravures'][i]['id'] + "\" aria-expanded=\"false\" aria-controls=\"collapse_text_" + val['gravures'][i]['id'] + "\">\n" +
            "    Voir les textes \n" +
            "  </button>";

        $elem2 += " <div class=\"collapse\" id=\"collapse_text_" + val['gravures'][i]['id'] + "\"> <div class=\"card card-body\"> <table class='table-bordered table-striped' style='width: 100%; font-size: 20px;'>";
        val['texts'].forEach(function (element) {
            if (element[val['gravures'][i]['id']] != undefined) { //vérifie que l'index de l'objet existe si oui ajout des textes
                $elem2 += "<tr>";
                $elem2 += "<td>" + element[val['gravures'][i]['id']]['name_block'] + "</td>";
                $elem2 += "<td> <div id=\"text_value_" + val['gravures'][i]['id'] + "_" + (element[val['gravures'][i]['id']]['name_block']).replace(/ /g,'') + "\">" + element[val['gravures'][i]['id']]['value'] + "</div></td>";
                $elem2 += "<td><button onclick=\"copyText(" + val['gravures'][i]['id'] + ",'" + (element[val['gravures'][i]['id']]['name_block']).replace(/ /g,'') + "');\" class='btn btn-default'><h3>Copier</h3></button></td>";
                $elem2 += "</tr>";
            }
        });
        $elem2 += "</table></div></div>";

        $elem2 += "<h2 style='background-color:#FF6B6B; color:white;'> Caisse " + val['gravures'][i]['box'] + "</h2>";
        $elem2 += "</div>";
    }
    $elem2 += "</div>";

    $elem2 += "<div id=\"div_msg_down_modal_" + val['chain_number'] + "\">";
    if (val['status'] == EN_COURS) {
        $elem2 += "<h3>Avez-vous bien rangé les produits ?</h3> " +
            "<button class='btn btn-success' onclick=\"setStatusGravureFinish(" + val['chain_number'] + ");\"><h3> J'ai rangé les produits</h3></button>" +
            "<button class='btn btn-danger' onclick=\"closeModalSerieGravure(" + val['chain_number'] + "); \"> " +
            "<h3>Retour </h3></button> " +
            "</div>";
    }
    else if(val['status'] == TERMINE){
        $elem2 += "<h3>Gravures terminées</h3>"
    }
    else {
        $elem2 += "<h3>Avez-vous bien lancé la gravure ? </h3>";
        $elem2 += "<b><h3> Illustrator -> Logiciel de gravure -> Machine Laser</h3></b>";
        $elem2 += "<h3> Vérifiez la correspondance entre les prévisualitions ci-dessus et les fichiers de votre plan de travail</h3>";
        $elem2 += "<button class='btn btn-success' onclick=\"setStatusGravureOnLoad(" + val['chain_number'] + ");\"><h3>J'ai lancé la gravure</h3></button>";
        $elem2 += "<button class='btn btn-danger' onclick=\"closeModalSerieGravure(" + val['chain_number'] + "); \"><h3>Retour</h3></button>";
    }
    $elem2 += "</div>";

    $elem2 += "</div>";
    $elem2 += "</div>";
    $elem2 += "</div>";
    $elem2 += "</div>";

    return $elem2;
}

function addModalForMachineMail(val) {

    $elem2 = "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Serie_Gravure_" + val['chain_number'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Serie_Gravure_Title_" + val['chain_number'] + "\" aria-hidden=\"true\">";
    $elem2 += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
    $elem2 += "<div class=\"modal-content\">";
    $elem2 += "<div class=\"modal-header\">";
    $elem2 += "<h3 class=\"modal-title\" id=\"Modal_Serie_Gravure_Title_" + val['chain_number'] + "\" style=\"color:" + val['color'] + "\"> Série " + val['surname'] + "s  </h3>";
    $elem2 += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
    $elem2 += "<i class='glyphicon glyphicon-remove'></i>";
    $elem2 += "</button>";
    $elem2 += "</div>";
    $elem2 += "<div class=\"modal-body\">";

    $elem2 += "<div class='row'>";

    for (var i = 0; i < val['gravures'].length; i++) {
        $elem2 += "<div class=\"col-sm-3\" align='center'>";
        $elem2 += "<img src=\"" + val['gravures'][i]['path_jpg'] + "\" style='max-height: 300px; max-width: 300px' >";
        $elem2 += "<i><h4 style='text-align: center;'>" + val['gravures'][i]['alias'] + "</h4></i>";

        $elem2 += "<table class='table-bordered table-striped' style='width: 100%; font-size: 20px;'>";

        val['texts'].forEach(function (element) {
            if (element[val['gravures'][i]['id']] != undefined) { //vérifie que l'index de l'objet existe si oui ajout des textes
                $elem2 += "<tr>";
                $elem2 += "<td>" + element[val['gravures'][i]['id']]['name_block'] + "</td>";
                $elem2 += "<td> <div id=\"text_value_" + val['gravures'][i]['id'] + "_" + (element[val['gravures'][i]['id']]['name_block']).replace(/ /g,'') + "\">" + element[val['gravures'][i]['id']]['value'] + "</div></td>";
                $elem2 += "<td><button onclick=\"copyText(" + val['gravures'][i]['id'] + ",'" + (element[val['gravures'][i]['id']]['name_block']).replace(/ /g,'') + "');\" class='btn btn-default'><h3>Copier</h3></button></td>";
                $elem2 += "</tr>";
            }
        });
        $elem2 += "</table>";
        $elem2 += "<h2 style='background-color:#FF6B6B; color:white;'> Caisse " + val['gravures'][i]['box'] + "</h2>";
        $elem2 += "</div>";
    }

    $elem2 += "</div>";

    $elem2 += "<div id=\"div_msg_down_modal_" + val['chain_number'] + "\">";
    if (val['status'] == EN_COURS) {
        $elem2 += "<h3>Avez-vous bien rangé les produits ?</h3> " +
            "<button class='btn btn-success' onclick=\"setStatusGravureFinish(" + val['chain_number'] + ");\"><h3> J'ai rangé les produits</h3></button>" +
            "<button class='btn btn-danger' onclick=\"closeModalSerieGravure(" + val['chain_number'] + "); \"> " +
            "<h3>Retour </h3></button> " +
            "</div>";
    }
    else if(val['status'] == TERMINE){
        $elem2 += "<h3>Gravures terminées</h3>"
    }
    else {
        $elem2 += "<h3>Avez-vous bien lancé la gravure ? </h3>";
        $elem2 += "<h3> Vérifiez la correspondance entre les prévisualitions ci-dessus et les fichiers de votre plan de travail</h3>";
        $elem2 += "<button class='btn btn-success' onclick=\"setStatusGravureOnLoad(" + val['chain_number'] + ");\"><h3>J'ai lancé la gravure</h3></button>";
        $elem2 += "<button class='btn btn-danger' onclick=\"closeModalSerieGravure(" + val['chain_number'] + "); \"><h3>Retour</h3></button>";
    }
    $elem2 += "</div>";

    $elem2 += "</div>";
    $elem2 += "</div>";
    $elem2 += "</div>";
    $elem2 += "</div>";

    return $elem2;
}

function isChecked(chain_number) {
    if( $(Checkbox_Engrave_Expe).is(':checked') && $(Checkbox_Engrave_Gift).is(':checked')  ) { //si les deux checkbox sont cochées
        buildTable(); //construit le tableau pour afficher les caisses
        createChainSession(); // construit la chaîne de série de gravure de la session en cours
        setTimeout(function () {
            setColorBlackForCaseFull();
        }, 4000);//remet les cases non vide à la même couleur

        $("#Modal_Serie_Gravure_" + chain_number).modal('hide'); //ferme la modal
        $("#Modal_Engrave_Finish").modal('hide');

    }
}

function copyText(idGravure, blockName) {
    var $textarea = $( '<textarea>' ); //construit un textarea
    $( 'body' ).append( $textarea ); //le fait apparaître
    $textarea.val( $("#text_value_" + idGravure + "_" + blockName ).text() ).select(); //Ajoute dans le textarea le contenu de la cible à copier et le selectionne
    document.execCommand( 'copy' ); //copie dans le presse papier
    $textarea.remove(); //suppression du textarea
    return false;
}

function cancelFinishEngrave(chain_number) {
    $.ajax({
        url: Routing.generate('gravure_cancel_finish', {chainNumber: chain_number}),
        success: function (result) {
            $("#Modal_Engrave_Finish").modal('hide');
        },
        error: function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

function cancelSession() {
    // $.ajax({
    //     url: Routing.generate('gravure_cancel_session'),
    //     success: function (result) {
    //         $("#Modal_Engrave_Finish").modal('hide');
    //     },
    //     error: function (result) {
    //         alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
    //     }
    // });
}

//ajout du nombre total de gravure de la session qui est alors terminé
function setNumberEngraveInSession() {
    $.ajax({
        url: Routing.generate('gravure_session_count_gravures'),
        success: function (result) {
        },
        error: function (result) {
            alert("Une erreur s'est produite avec l'insertion du total de gravure, veuillez contacter l'administrateur.");
        }
    });
}
