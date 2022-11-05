/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// FOS JS Routing
import Routing from 'fos-router';
const routes = require('./js/routes.json');
Routing.setRoutingData(routes);
// Make Routing available globally
window.Routing = Routing;