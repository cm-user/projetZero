var array_box = []; //tableau stockant les id_prestashop des commandes
var color_machine = "";

buildTable(); //construit le tableau pour afficher les caisses
createChainSession(); // construit la chaîne de série de gravure de la session en cours
getColorMachineDefault(); //renseigne la couleur de la machine par défaut

function createChainSession() {
    $.ajax({
        url: Routing.generate('gravure_assistant_begin'), //enregistre en bdd
        success: function (result) {
            $elem = "<table><thead><td>Nb</td><td>Produits</td><td>Sélec.</td></thead><tbody>";
            $.each(result, function (key, val) {
                $elem += "<tr>";
                $elem += "<td>" + val['number'] + "</td>";
                $elem += "<td>" + val['surname'] + "</td>";
                $elem += "</tr>";
            });
            $elem += "</tody></table>";
            $("#div_chain_category").html($elem);

        }
    });

}

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
        url: Routing.generate('gravure_chain_number_json'), //enregistre en bdd
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

    $elem = "<div class='row' style='margin-left: 30px;'><table><thead><th style='font-size:50px;background-color: #EFE7E7;'>" + array_gravure[0]['id_prestashop'] + "</th><th style='font-size:20px;background-color:#EEDEDE;padding: 10%; '>Machine</th></thead><tbody>";

    $("#case" + numberBox).html(numberBox); //affiche le numéro de caisse
    for (i=0; i < array_gravure.length; i++){
        $("#case" + numberBox).addClass("chain_" + array_gravure[i]['chain_number']); //ajout d'une classe avec le numéro de chaine
        if(array_gravure[i]['colorCategory'] != null){
            $elem += "<tr style=\"background-color:" + array_gravure[i]['colorCategory'] + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>"
            $elem += "<td></td></tr>";
        }
        else if(array_gravure[i]['colorGravure'] != null){
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + array_gravure[i]['colorGravure'] + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "<td style='padding: 3%;'><a onclick=\"setColorMachineDefault(" + array_gravure[i]['id'] +");\"><i class=\"glyphicon glyphicon-check\" style=\"font-size:80px; padding: 25%;color: lightgrey;\"></i></a></td></tr>";
        }
        else {
            $elem += "<tr id=\"row_gravure_" + array_gravure[i]['id'] + "\" style=\"background-color:" + color_machine + ";\"><td style='padding: 3%;'><img src=\""+ array_gravure[i]['jpg'] + "\" width='180'></div></td>";
            $elem += "<td style='padding: 3%;'><a onclick=\"setColorMachineDefault(" + array_gravure[i]['id'] +");\"><i class=\"glyphicon glyphicon-check\" style=\"font-size:80px; padding: 25%;color: lightgrey;\"></i></a></td></tr>";
        }

        addListenerClic(numberBox); //ajout d'un listener au click affiche les images
    }
    $elem += "</tbody></table></div>";

    $("#DisplayCase_" + numberBox).append($elem);
}

//ajout d'un listener au click affiche les images
function addListenerClic(number) {
    $("#case" + number).click(function(){
        $("#div_display_gravure > div").hide(); // cache toutes les images
        $("#table_order td").css("background-color", "#E1B7B9"); //remet toutes les cases à la m
        $("#case" + number).css("background-color", "#D82228");
        $("#DisplayCase_" + number).show();
        // $("#div_display_case").css('width', '225px');

    });
}

//renseigne la couleur de la machine par défaut
function getColorMachineDefault() {
    $.ajax({
        url: Routing.generate('machine_default_color'),
        success: function (result) {
            color_machine = result ;
        }
    });
}

//Modifie la couleur de la ligne par la couleur de la machine par défaut
function setColorMachineDefault(idGravure) {
    $("#row_gravure_" + idGravure).css("background-color", color_machine); //change la couleur de la ligne ciblée
}





