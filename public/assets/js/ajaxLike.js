$(document).ready(function() {
    function onClickBtnLike(event) {
        // Bloquer le comportement par défaut  du lien
        event.preventDefault();

        // Capturer le lien et déclarer les variables a changer
        const url = this.href;
        const spanCount = $(this).find('span.js-likes');
        const icone = $(this).find('i');
        const label =$(this).find('span.js-label');

        // Appeler Symfony et switcher les variables
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                const likes = response.likes;
                spanCount.text(likes);

                if (icone.hasClass('fa-heart')) {
                    icone.removeClass('fa-heart').addClass('fa-heart-o');
                    label.text('J\'aime');
                } else {
                    icone.removeClass('fa-heart-o').addClass('fa-heart');
                    label.text('Je n\'aime plus');

                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    $('a.js-like').on('click', onClickBtnLike);
});