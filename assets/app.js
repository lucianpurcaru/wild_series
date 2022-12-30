/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';


import { Tooltip, Toast, Popover } from 'bootstrap';

// start the Stimulus application
import './bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css'; 

document.getElementById('watchlist').addEventListener('click', addToWatchlist);
function addToWatchlist(event) {
    event.preventDefault();
    
    let watchlistLink = event.currentTarget;
    let link = watchlistLink.href;

    fetch(link)
        .then(res => res.json())
        .then(function(res) {
            let watchlistIcon = watchlistLink.firstElementChild;
            if (res.isInWatchlist) {
                watchlistIcon.classList.remove('bi-heart'); 
                watchlistIcon.classList.add('bi-heart-fill'); 
            } else {
                watchlistIcon.classList.remove('bi-heart-fill');
                watchlistIcon.classList.add('bi-heart'); 
            }
            console.log('passe');
        });
}