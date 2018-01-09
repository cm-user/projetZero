//
// function modal(){
//
// }
//
// $('#myModal').on('shown.bs.modal', function () {
//     $('#myInput').focus()
// })
//
// alert();

function goPiece(){
    var detail=$('#detail').val(); //recupére le contenu du champ
    console.log(detail);
    $('#MyModal').modal('hide'); //ferme la modal (non nécessaire si on fait une redirection)
}