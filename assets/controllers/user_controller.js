import { Controller } from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    static values = {
        switchUrl: String,
        verifyUrl: String,
    };

    banned = `Блокировка: <span class="text-danger">забанен</span>`;
    notBanned = `Блокировка: <span class="text-success">нет</span>`;

    verified = `<p>Статус почты: <span class="text-success">подтверждена</span></p>`;
    notVerified = `<p>Статус почты: <span class="text-danger">не подтверждена</span></p>`;

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

    verify(event) {
        let verifyStatus = document.getElementById('verify-status');
        let verified = this.verified;
        let notVerified = this.notVerified;

        axios.post(this.verifyUrlValue)
            .then(function (response) {
                if (response.data.success) {
                    event.target.remove();
                    verifyStatus.innerHTML = verified;
                }
            });
    }

    addBadge(event) {
        let params = new URLSearchParams();
        params.append('id', 132);

        axios.post('/post-test', params).then(function (response) {
            console.log(response.data);
        });
    }
}