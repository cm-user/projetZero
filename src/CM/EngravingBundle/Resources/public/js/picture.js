/**
 * Created by Okaou on 28/11/2017.
 */
var url="http://localhost/projetZero/web/app_dev.php/";

function recherche(){
    var val = $("#recherche").val();
    console.log(val);
    //va chercher les images dont le nom est cet idOrder
    $.getJSON( url+"engraving/picture/json/" + val,function(data){
        console.log(data);
        if (data == "error"){
            displayError();
        }
        else {
            displayPicture(data);
        }

    });

}

function displayPicture(data){
    var $elem = "<table class=\"table table-striped table-bordered\"> <thead> <tr> <th>Id</th>  <th>Catégorie</th> <th>Surnom</th> <th>Session</th> <th>Machine utilisé</th> <th>Id_config</th> <th>Chemin du jpg</th> <th>Chemin du pdf</th><th>Date de création</th> <th>Date dernière modification</th> <th>Image</th> </tr>  </thead> <tbody> ";
    $.each(data, function (key, val) {
        $elem += "<tr>";
        $elem += "<td>" + val['id'] + "</td>";
        $elem += "<td>" + val['category'] +"</td>";
        $elem += "<td>" + val['surname'] +"</td>";
        $elem += "<td><a href=\'/engraving/session/" + val['id'] + "\'>" + val['session'] +"</a></td>";
        $elem += "<td>" + val['machine'] +"</td>";
        $elem += "<td>" + val['id_config'] +"</td>";
        $elem += "<td><a href=\'" + val['path-jpg'] + "\' >" + val['path-jpg'] +"</a></td>";
        $elem += "<td><a href=\'" + val['path-pdf'] + "\' >" + val['path-pdf'] +"</a></td>";
        $elem += "<td>" + val['date_created']['date'] +"</td>";
        $elem += "<td>" + val['date_updated']['date'] +"</td>";
        $elem += "<td><img src=\""+val['path-jpg'] + " \" width=\'200\'></td>";
        $elem += "</tr>";

    });

    $elem += " </tbody></table> ";
    // $elem += "<button type=\"button\" id=\"delete\" class=\"btn btn-danger\"> Fermer</button>";
    console.log($elem);
    $elem +="<br><br>";
    $( "#show" ).html($elem);

    // //fonction pour vider la div au clic du bouton Retour
    // $("#delete").click(function () {  //
    //     $( "#show" ).html("");
    // });
}

function displayError(){
    console.log("coucou");
    var $elem = "<div class=\"alert alert-info\" role=\"alert\">   Aucune gravure avec ce numéro de commande dans la base de donnée local </div>";
    $("#show").html($elem);
}