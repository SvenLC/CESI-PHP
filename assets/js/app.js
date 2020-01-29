/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "../css/app.scss";
import $ from "jquery";
const routes = require("../../public/js/fos_js_routes.json");
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

Routing.setRoutingData(routes);

window.addEventListener("DOMContentLoaded", event => {
  console.log("Page loaded");
});

const favBtns = document.getElementsByClassName("fav-btn");

favBtns.forEach(favBtn => {
  favBtn.addEventListener("click", addFavorite);
});


function addFavorite(event) {
  const xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     console.log('Request send')
    }
  };
  const method = "GET"
  const url = "add/favorite/" + event.target.value
  xhttp.open(method, url, true);
  xhttp.send();
  console.log(event.target.value);
}
