$('#add-image').click(function () {
    //Je compte le nombre de champs déjà créés dans l'ajout d'images
    //pour créer le suivant
    const index = +$('#widgets-counter').val();
    //je récupère le prototype de l'entrée (data-prototype)
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    // j'injecte le code au sein de la div
    $('#annonce_images').append(tmpl);
    $('widgets-counter').val(index + 1);
    // je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);
}
updateCounter();
handleDeleteButtons();


