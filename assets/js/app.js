/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
import $ from 'jquery';
import 'bootstrap';

//Affiche le nom de l'image dans le champ à gauche du bouton browse (ce n'est pas le cas par défaut)
$('.custom-file-input').on('change', function(e) {
	var inputFile = e.currentTarget;
	$(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
})