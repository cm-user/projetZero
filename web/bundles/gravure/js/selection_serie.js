var color_machine = ""; //contient la couleur de la machine sélectionné
var color_chain = ""; //contient la couleur de la chaîne en cours

buildTable(); //construit le tableau pour afficher les caisses
createChainSession(); // construit la chaîne de série de gravure de la session en cours
getColorMachineDefault(); //renseigne la couleur de la machine par défaut
setTimeout(function(){ setColorBlackForCaseFull(); },2000);//remet les cases non vide à la même couleur


function createChainSession() {
    $.ajax({
        url: Routing.generate('selection_serie_chain'),
        success: function (result) {
            $elem = "<table><thead style='background-color: #EFEFEF;'><td>Nb</td><td>Produits</td><td>Modifier</td></thead><tbody>";
            $.each(result, function (key, val) {
                $elem += "<tr style=\"background-color: " + val['color'] + ";\" id=\"chain_number_" + (key+1) + "\">";
                $elem += "<td>" + val['gravures'].length + "</td>";
                $elem += "<td><a style=\"display:block;width:100%;height:100%; cursor: pointer;\" onclick=\"addListenerChangeColorCase("+ (key+1) +",'" +val['color'] +"');\">" + val['surname'] + "</a></td>";
                $elem += val['locked_machine'] == 0 ? "<td><button class='btn-picto' onclick=\"setArrayColorMachineDefault([" + convertObjectToArray(val['gravures']) + "],'" + (key+1) + "');\"><i class=\"glyphicon glyphicon-retweet\" style=\"\"></i></button> </td>" : "<td></td>";
                $elem += "</tr>";
            });
            $elem += "</tody></table>";
            $("#div_chain_category").html($elem);

        },
        error:function (result) {
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
        error:function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
    setTimeout(function(){ hydrateTable(); },500); //remplit le tableau avec les numéros de caisses
}


function setMachineUsed(id) {
    $('.machine').css('opacity', '0.2');
    $.ajax({
        url: Routing.generate('machine_use_session', {id: id}), //enregistre en bdd
        success: function (result) {
            color_machine = result ;
            $('#btn_machine_' + id).css('opacity', '1');
        },
        error:function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

//remplit le tableau avec les numéros de caisses
function hydrateTable() {
    var id_order = ""; //nom de la catégorie
    var old_id_order = ""; //nom de la catégorie antérieur
    var array_gravure = [];
    $.ajax({
        url: Routing.generate('gravure_chain_number_json'),
        success: function (result) {
            $.each(result, function (key, val) {
                //à la première itération on recupère le numéro de commande
                id_order = val['id_prestashop'];

                //vérifie que la catégorie précédente était différente, si c'est le cas on crée un nouveau tableau
                if (id_order != old_id_order && old_id_order != "") {
                    addListenerCase(array_gravure); //ajout du numéro de caisse au tableau
                    array_gravure = []; //vide le tableau
                }
                if(result.length-1 == key){
                    array_gravure.push({'jpg' : val['jpg'], 'colorGravure':val['colorGravure'], 'colorCategory':val['colorCategory'], 'id':val['id'], 'chain_number':val['chain_number'], 'box':val['box'], 'id_prestashop':val['id_prestashop'], 'alias':val['alias'], 'locked':val['locked']}); //ajout dans le tableau les id des gravures
                    addListenerCase(array_gravure); //ajout du numéro de caisse au tableau
                    array_gravure = []; //vide le tableau
                }

                array_gravure.push({'jpg' : val['jpg'], 'colorGravure':val['colorGravure'], 'colorCategory':val['colorCategory'], 'id':val['id'], 'chain_number':val['chain_number'], 'box':val['box'], 'id_prestashop':val['id_prestashop'], 'alias':val['alias'], 'locked':val['locked']}); //ajout dans le tableau les id des gravures
                old_id_order = val['id_prestashop'];

            });
        },
        error:function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

function addListenerCase(array_gravure) {
    var numberBox = array_gravure[0]['box'];

    $elem = "<div class='row' style='margin-left: 30px;'><table><thead><th style='font-size:50px;background-color: #EFE7E7;'>" + array_gravure[0]['id_prestashop'] + "</th><th style='font-size:20px;background-color:#EEDEDE;padding: 10%;width:160px; '>Modifier</th></thead><tbody>";

    $("#case" + numberBox).html(numberBox); //affiche le numéro de caisse
    for (i=0; i < array_gravure.length; i++){
        $("#case" + numberBox).addClass("chain_" + array_gravure[i]['chain_number']); //ajout d'une classe avec le numéro de chaine
        if(array_gravure[i]['locked'] == 0){
            if(array_gravure[i]['colorCategory'] !== ""){
                $elem += "<tr style=\"background-color:" + array_gravure[i]['colorCategory'] + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a>";
                $elem += "<h4>" + array_gravure[i]['alias'] +"</h4></div></td>";
                $elem += "<td></td></tr>";
            }
            else if(array_gravure[i]['colorGravure'] !== ""){
                $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + array_gravure[i]['colorGravure'] + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a>";
                $elem += "<h4>" + array_gravure[i]['alias'] +"</h4></div></td>";
                $elem += "<td style='padding: 3%;'><button class='btn-picto' onclick=\"setColorMachineSession(" + array_gravure[i]['id'] +"," + 0 + ");\"><i class=\"glyphicon glyphicon-retweet\" style=\"font-size:60px; padding: 25%;color: lightgrey;width:100px;\"></i></button></td></tr>";
            }
            else {
                $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + color_machine + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a></div></td>";
                $elem += "<td style='padding: 3%;'><button class='btn-picto' onclick=\"setColorMachineSession(" + array_gravure[i]['id'] +"," + 0 + ");\"><i class=\"glyphicon glyphicon-retweet\" style=\"font-size:60px; padding: 25%;color: lightgrey;width:100px;\"></i></button></td></tr>";
            }
        }
        else{
            if(array_gravure[i]['colorCategory'] !== ""){
                $elem += "<tr style=\"background-color:" + array_gravure[i]['colorCategory'] + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a>";
                $elem += "<h4>" + array_gravure[i]['alias'] +"</h4></div></td>";
                $elem += "<td></td></tr>";
            }
            else if(array_gravure[i]['colorGravure'] !== ""){
                $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + array_gravure[i]['colorGravure'] + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a>";
                $elem += "<h4>" + array_gravure[i]['alias'] +"</h4></div></td>";
                $elem += "<td></td></tr>";
            }
            else {
                $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + color_machine + ";\"><td style='padding: 3%;'><a href=\"#\" data-toggle=\"modal\" data-target=\"#Modal_Picture_" + array_gravure[i]['id'] + "\"><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></a></div></td>";
                $elem += "<td></td></tr>";
            }
        }

        //modal
        $elem += "<div class=\"modal fade bd-example-modal-lg\" id=\"Modal_Picture_" + array_gravure[i]['id'] + "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"Modal_Picture_title_" + array_gravure[i]['id'] + "\" aria-hidden=\"true\">";
        $elem += "<div class=\"modal-dialog modal-lg\" role=\"document\">";
        $elem += "<div class=\"modal-content\">";
        $elem += "<div class=\"modal-header\">";
        $elem += "<h5 class=\"modal-title\" id=\"Modal_Picture_title_" + array_gravure[i]['id'] + "\"></h5>";
        $elem += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
        $elem += "<span aria-hidden=\"true\">&times;</span>";
        $elem += "</button>";
        $elem += "</div>";
        $elem += "<div class=\"modal-body\">";
        $elem += "<img src=\"" + array_gravure[i]['jpg'] + " \" width=\'750\'>";
        $elem += "</div>";
        $elem += "<div class=\"modal-footer\">";
        $elem += "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fermer</button>";
        $elem += "</div>";
        $elem += "</div>";
        $elem += "</div>";
        $elem += "</div>";
    }



    $elem += "</tbody></table></div>";
    $("#DisplayCase_" + numberBox).html($elem);

    addListenerClic(numberBox); //ajout d'un listener au click affiche les images

}

//ajout d'un listener au click affiche les images
function addListenerClic(number) {
    $("#case" + number).click(function(){
        var array_number_case = []; //tableau contenant les numéros de cases lié à la chaîne

        $("#div_display_gravure > div").hide(); // cache toutes les images

        if($("#case" + number).css("background-color") !== "rgb(0, 0, 0)"){
            //parcours de toutes les cases pour récupérer uniquement les cases liées à la chaîne
            $("#table_order td" ).each(function( i ) {
                if ( this.style.backgroundColor !== "black" && this.style.backgroundColor !== "" ) { //vérifie que la couleur ne soit ni noir ni celle par défaut
                    array_number_case.push((i+1));
                }
            });
            setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
            //met les case liées à la chaîne à la même couleur que cette dernière
            array_number_case.forEach(function(element) {
                $("#case" + element).css("background-color", color_chain); //change la couleur des cases par celle de la chaîne
            });

            array_number_case = []; // vide le tableau
        }
        else{
            $("#div_chain_category tbody tr").css("opacity", "1"); //remet l'opacité à 1 pour toutes les lignes des chaînes
            setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
        }
        $("#case" + number).css("opacity", "0.5"); //change uniquement la couleur de la caisse
        $("#case" + number).css("border-radius", "50%"); //change uniquement le border de la caisse
        $("#DisplayCase_" + number).show(); //affiche les gravures de la case

    });
}

//renseigne la couleur de la machine par défaut à la session et à la variable color_machine
function getColorMachineDefault() {
    $.ajax({
        url: Routing.generate('machine_default_color'),
        success: function (result) {
            color_machine = result['color'] ; //renseigne la couleur à la variable
            setMachineUsed(result['id']); //met en évidence le bouton lié à la machine par défaut
        },
        error:function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
}

//Modifie la couleur de la ligne par la couleur de la machine de la session
function setColorMachineSession(idGravure, bool) {
    $.ajax({
        url: Routing.generate('gravure_change_machine_default', {id : idGravure}),
        success: function (result) {
        },
        error:function (result) {
            alert("Une erreur s'est produite avec le serveur, veuillez actualiser la page.");
        }
    });
    $("#row_gravure_" + idGravure).css("background-color", color_machine); //change la couleur de la ligne ciblée
    //si bool vaut 0, cela signifie qu'une seule gravure change de machine, on met donc à jour le tableau des chaînes
    if(bool == 0){
        updateChainSessionAndTable();
        setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
        numberCase = $("#row_gravure_" + idGravure).parent().closest('div').parent().attr('id').replace("DisplayCase_", ""); //cherche le numéro de case de la gravure
        $("#case" + numberCase).css("background-color", color_machine); //change uniquement la couleur de la caisse
    }
}

//modifie la couleur de toutes les gravures liée à la chaîne
function setArrayColorMachineDefault(gravures, number){
    for(i=0;i<gravures.length;i++){
        setColorMachineSession(gravures[i], 1);
    }
    setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
    $(".chain_"+number).css("background-color", color_machine); //change de couleur les cases contenant une gravure de la chaîne
    $(".chain_"+number).css("opacity", "1"); //remet l'opacité pour ces cases à 1
    color_chain = color_machine; // ajout de la couleur de la chaîne cliqué dans la variable color_chain
    updateChainSessionAndTable();
}

//Change la couleur des cases en fonction de la machine au clic sur une catégorie
function addListenerChangeColorCase(number, color) {
    $("#div_chain_category tbody tr").css("opacity", "0.5"); //baisse l'opacité de toutes les lignes des chaînes
    $("#chain_number_" + number).css("opacity", "1"); //augmente l'opacité de la ligne cliqué
    color_chain = color; // ajout de la couleur de la chaîne cliqué dans la variable color_chain

    $("#div_display_gravure > div").hide(); //masque les gravures
    setColorBlackForCaseFull(); //remet les cases non vide à la même couleur
    $(".chain_"+number).css("background-color", color); //change de couleur les cases contenant une gravure de la chaîne

    var case_number = $(".chain_"+number).length;//nombre de case liée à la chaîne cliqué
    if(case_number == 1){ //si il n'y a qu'une seule case on affiche le contenu lié
        var case_digit = $(".chain_"+number).attr('id').replace("case", ""); //numéro de la case
        $("#DisplayCase_" + case_digit).show(); //affiche les gravures de la case
    }

    $(".chain_"+number).css("opacity", "1"); //remet l'opacité pour ces cases à 1
    $(".chain_"+number).css("border-radius", "0%"); //remet l'opacité pour ces cases à 1

}

//Mise à jour du tableau contenant les chaînes et du tableau pour les caisses
function updateChainSessionAndTable() {
    setTimeout(function(){ createChainSession(); },500); //maj des chaînes
    // $('#table_order td').removeClass(); //supprime les classes des cases avant de les mettre à jour
    $('#table_order td').removeAttr('class').attr('class', '');  //supprime les classes des cases avant de les mettre à jour
    setTimeout(function(){ hydrateTable(); },1000); //maj des données dans le tableau
}

//met en noir les cases contenant les numéros
function setColorBlackForCaseFull() {
    $("#table_order td" ).each(function( i ) {
        if ( $(this).html() != "") { //vérifie que la cellule contienne bien un numéro
            $(this).css("background-color", "black"); //change le fond en noir
            $(this).css("opacity", "1"); //remet l'opacité à 1
            $(this).css("border-radius", "0%"); //remet le border radius à 0
        }
    });
}

//Téléchargement des pdf, envoie du mail, et affichage des messages à l'utilisateur avant de commençer à graver
function beginSessionGravure(){
    $('#Modal_Alert').modal();
    $("#Modal_Alert").modal('show');
}

function convertObjectToArray(gravures){
    var arrayResponse = [];
    gravures.forEach(function(elem){
        arrayResponse.push(elem['id']);
    });

    return arrayResponse;
}