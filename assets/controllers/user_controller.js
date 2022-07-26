import { Controller } from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    static values = {
        switchUrl: String,
    };

    banned = `Блокировка: <span class="text-danger">забанен</span>`;
    notBanned = `Блокировка: <span class="text-success">нет</span>`;

    switchUserStatus(event) {
        let banStatus = document.getElementById('ban-status');
        let banned = this.banned;
        let notBanned = this.notBanned;


        axios.post(this.switchUrlValue)
            .then(function (response) {
                if (response.data.error) {
                    return;
                }

                if (response.data.newStatus) {
                    event.target.innerHTML = "Разблокировать";
                    event.target.classList.replace('btn-outline-warning', 'btn-outline-success');
                    banStatus.innerHTML = banned;
                }

                if(!response.data.newStatus) {
                    event.target.innerHTML = "Заблокировать";
                    event.target.classList.replace('btn-outline-success', 'btn-outline-warning');
                    banStatus.innerHTML = notBanned;
                }
            });
    }
}