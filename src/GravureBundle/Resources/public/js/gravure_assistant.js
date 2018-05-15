var array_box = []; //tableau stockant les id_prestashop des commandes
var color_machine = ""; //contient la couleur de la machine sélectionné

buildTable(); //construit le tableau pour afficher les caisses
createChainSession(); // construit la chaîne de série de gravure de la session en cours
getColorMachineDefault(); //renseigne la couleur de la machine par défaut

function createChainSession() {
    $.ajax({
        url: Routing.generate('gravure_assistant_begin'),
        success: function (result) {
            $elem = "<table><thead style='background-color: #EFEFEF;'><td>Nb</td><td>Produits</td><td>Modifier</td></thead><tbody>";
            $.each(result, function (key, val) {
                $elem += "<tr style=\"background-color: " + val['color'] + ";\" id=\"chain_number_" + (key+1) + "\">";
                $elem += "<td>" + val['number'] + "</td>";
                $elem += "<td><a onclick=\"addListenerChangeColorCase("+ (key+1) +",'" +val['color'] +"');\">" + val['surname'] + "</a></td>";
                $elem += val['locked'] == 0 ? "<td><button class='btn-picto' onclick=\"setArrayColorMachineDefault([" + val['gravures'] + "]);\"><i class=\"glyphicon glyphicon-retweet\" style=\"\"></i></button> </td>" : "<td></td>";
                $elem += "</tr>";
            });
            $elem += "</tody></table>";
            $("#div_chain_category").html($elem);

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
                array_box.push(0);
            }
            $("#div_display_gravure").html($divDisplayCase);
            $("#div_table").html($elem);

        }
    });
    setTimeout(function(){ hydrateTable(); },2000); //remplit le tableau avec les numéros de caisses
}


function setMachineUsed(id) {
    $('.machine').css('opacity', '0.2');
    $.ajax({
        url: Routing.generate('machine_use_session', {id: id}), //enregistre en bdd
        success: function (result) {
            color_machine = result ;
            $('#btn_machine_' + id).css('opacity', '1');
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
                else if(result.length-1 == key){
                    array_gravure.push({'jpg' : val['jpg'], 'colorGravure':val['colorGravure'], 'colorCategory':val['colorCategory'], 'id':val['id'], 'chain_number':val['chain_number'], 'box':val['box'], 'id_prestashop':val['id_prestashop']}); //ajout dans le tableau les id des gravures
                    addListenerCase(array_gravure); //ajout du numéro de caisse au tableau
                    array_gravure = []; //vide le tableau
                }

                array_gravure.push({'jpg' : val['jpg'], 'colorGravure':val['colorGravure'], 'colorCategory':val['colorCategory'], 'id':val['id'], 'chain_number':val['chain_number'], 'box':val['box'], 'id_prestashop':val['id_prestashop']}); //ajout dans le tableau les id des gravures
                old_id_order = val['id_prestashop'];

            });
            // $("#div_chain_category").html($elem);

        }
    });
}

function addListenerCase(array_gravure) {

    var numberBox = array_gravure[0]['box'];

    $elem = "<div class='row' style='margin-left: 30px;'><table><thead><th style='font-size:50px;background-color: #EFE7E7;'>" + array_gravure[0]['id_prestashop'] + "</th><th style='font-size:20px;background-color:#EEDEDE;padding: 10%;width:160px; '>Modifier</th></thead><tbody>";

    $("#case" + numberBox).html(numberBox); //affiche le numéro de caisse
    for (i=0; i < array_gravure.length; i++){
        $("#case" + numberBox).addClass("chain_" + array_gravure[i]['chain_number']); //ajout d'une classe avec le numéro de chaine
        if(array_gravure[i]['colorCategory'] != null){
            $elem += "<tr style=\"background-color:" + array_gravure[i]['colorCategory'] + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "<td></td></tr>";
        }
        else if(array_gravure[i]['colorGravure'] != null){
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + array_gravure[i]['colorGravure'] + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "<td style='padding: 3%;'><button class='btn-picto' onclick=\"setColorMachineSession(" + array_gravure[i]['id'] +"," + 0 + ");\"><i class=\"glyphicon glyphicon-retweet\" style=\"font-size:60px; padding: 25%;color: lightgrey;\"></i></button></td></tr>";
        }
        else {
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + color_machine + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "<td style='padding: 3%;'><button class='btn-picto' onclick=\"setColorMachineSession(" + array_gravure[i]['id'] +"," + 0 + ");\"><i class=\"glyphicon glyphicon-retweet\" style=\"font-size:60px; padding: 25%;color: red;\"></i></button></td></tr>";
        }

        addListenerClic(numberBox); //ajout d'un listener au click affiche les images
    }
    $elem += "</tbody></table></div>";

    $("#DisplayCase_" + numberBox).html($elem);
}

//ajout d'un listener au click affiche les images
function addListenerClic(number) {
    $("#case" + number).click(function(){
        var array_number_case = []; //tableau contenant les numéros de cases lié à la chaîne

        $("#div_display_gravure > div").hide(); // cache toutes les images

        $("#table_order td" ).each(function( i ) {
            if ( this.style.backgroundColor !== "rgb(225, 183, 185)" ) {
                array_number_case.push((i+1));
            }
            $(this).css("opacity", "1"); //remet l'opacité à 1
        });

        $("#table_order td").css("background-color", "#E1B7B9"); //remet toutes les cases à la même couleur

        $.each(array_number_case, function( index, value ) {
            $("#case" + value).css("background-color", color_machine); //change uniquement la couleur de la caisse
            $("#case" + value).css("opacity", "0.5"); //met l'opacité à 0.5 pour les cases lié à la chain
        });

        array_number_case = []; //vide le tableau

        //vérifie que la case cliqué soit lié à la chain en cours
        if($("#case" + number).css("background-color") !== "rgb(225, 183, 185)"){
            $("#case" + number).css("opacity", "1"); //met l'opacité à 1 pour la case cliqué
        }
        else { //si l'utilisateur clique sur une case lié à une autre chain
            $("#table_order td").css("background-color", "#E1B7B9"); //remet toutes les cases à la même couleur
            $("#table_order td").css("opacity", "1"); //remet l'opacité à 1 pour toutes les cases
            $("#case" + number).css("background-color", color_machine); //change uniquement la couleur de la caisse
            $("#div_chain_category tbody tr").css("opacity", "1"); //remet l'opacité à 1 pour toutes les lignes des chaînes
        }


        $("#DisplayCase_" + number).show();
        // $("#div_display_case").css('width', '225px');

    });
}

//renseigne la couleur de la machine par défaut à la session et à la variable color_machine
function getColorMachineDefault() {
    $.ajax({
        url: Routing.generate('machine_default_color'),
        success: function (result) {
            color_machine = result['color'] ; //renseigne la couleur à la variable
            setMachineUsed(result['id']); //met en évidence le bouton lié à la machine par défaut
        }
    });
}

//Modifie la couleur de la ligne par la couleur de la machine de la session
function setColorMachineSession(idGravure, bool) {
    $.ajax({
        url: Routing.generate('gravure_change_machine_default', {id : idGravure}),
        success: function (result) {
            console.log(result);
        }
    });
    $("#row_gravure_" + idGravure).css("background-color", color_machine); //change la couleur de la ligne ciblée
    //si bool vaut 0, cela signifie qu'une seule gravure change de machine, on met donc à jour le tableau des chaînes
    if(bool == 0){
        updateChainSessionAndTable();
    }
    // $("#div_display_gravure > div").hide(); //masque les gravures
    // $("#table_order td").css("background-color", "#E1B7B9"); //remet toutes les cases à la même couleur
}

//modifie la couleur de toutes les gravures liée à la chaîne
function setArrayColorMachineDefault(gravures){
    for(i=0;i<gravures.length;i++){
        setColorMachineSession(gravures[i], 1);
    }
    updateChainSessionAndTable();
}

//Change la couleur des cases en fonction de la machine au clic sur une catégorie
function addListenerChangeColorCase(number, color) {
    $("#div_chain_category tbody tr").css("opacity", "0.7"); //baisse l'opacité de toutes les lignes des chaînes
    $("#chain_number_" + number).css("opacity", "1"); //augmente l'opacité de la ligne cliqué

    $("#div_display_gravure > div").hide(); //masque les gravures
    $("#table_order td").css("background-color", "#E1B7B9"); //remet toutes les cases à la même couleur
    $(".chain_"+number).css("background-color", color); //change de couleur les cases contenant un produit de la catégorie
    $(".chain_"+number).css("opacity", "1"); //remet l'opacité pour ces cases à 1

}

//Mise à jour du tableau contenant les chaînes et du tableau pour les caisses
function updateChainSessionAndTable() {
    setTimeout(function(){ createChainSession(); },1000); //maj des chaînes
    $('#table_order td').removeClass(); //supprime les classes des cases avant de les mettre à jour
    setTimeout(function(){ hydrateTable(); },2000); //maj des données dans le tableau
}