$('document').ready(function(){
    Traitement();
})

$('#plan_formation_formation').on('change', function(){
    Traitement();
})

$('#plan_formation_client').on('change', function(){
    Traitement();
});

function Traitement(){
    $.ajax({
        url: $('form').data('action') + '/' + $('#plan_formation_client option:selected').val() + '/' + $('#plan_formation_formation option:selected').val(),
    }).done(function(data){
        dataClient = data.split('/');
        tableau = `
        <table class="table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Heures de Comptabilité</th>
                    <th>Heures de Bureautique</th>
                    <th>Type de Formation</th>
                    <th>Nombre d'heures</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>`+dataClient[0]+`</td>
                    <td><strong><span style="color: `+ dataClient[7] +`" >`+dataClient[5]+`</span>/`+dataClient[1]+`</stron></td>
                    <td><strong><span style="color: `+ dataClient[8] +`" >`+dataClient[6]+`</span>/`+dataClient[2]+`</stron></td>
                    <td>`+dataClient[3]+`</td>
                    <td>`+dataClient[4]+`</td>
                </tr>
            </tbody>
        </table>`
        if(dataClient[7] == "red" || dataClient[8] == "red"){
            $("#plan_formation_Valider").attr('disabled', true);
        }else{
            $("#plan_formation_Valider").attr('disabled', false);
        }
        $('#tableau').html(tableau)    
    })
}