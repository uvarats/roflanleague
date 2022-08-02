import { Controller } from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    static values = {
        addUrl: String,
        getUrl: String,
        removeUrl: String,
    };
    static targets = [];

    page;

    spinner = `<div class="spinner-border" role="status">
                   <span class="visually-hidden">Loading...</span>
                </div>`;

    connect() {
        this.page = 1;
    }

    addUser(event) {
        this.userAction(event, this.addUrlValue)
    }

    removeUser(event) {
        this.userAction(event, this.removeUrlValue);
    }

    userAction(event, url) {
        let userId = event.target.value;
        let userElement = event.target.closest('.user-object');
        let oldButtonData = event.target.innerHTML;

        event.target.innerHTML = this.spinner;
        event.target.disabled = true;

        let data = new URLSearchParams();
        data.append('id', userId);

        axios.post(url, data)
            .then(function (response) {
            if (response.data.success) {
                userElement.remove();
            }
            if (response.data.error) {
                event.target.innerHTML = oldButtonData;
                event.target.disabled = false;
            }
        });
    }

    getAdditionalUsers(event) {
        this.page++;

        let data = new URLSearchParams();
        data.append('page', this.page);

        axios.post(this.getUrlValue, data)
            .then(function (response) {
            if (response.data.length > 0) {
                let container = document.getElementById('users-container');
                response.data.forEach(function (element) {
                    let div = document.createElement('div');
                    div.classList.add('user-object');
                    div.innerHTML =
                        `<div class="row d-flex align-items-center justify-content-between row-cols-md-2 row-cols-1">
                            <div class="d-flex flex-row col">
                                <img class="rounded-circle" width="64" height="64" src="https://cravatar.eu/avatar/${element['name']}/128">
                                <span class="h2 ms-3">${element['name']}</span>
                            </div>
                            <div class="col d-flex flex-row justify-content-end">
                                <button class="btn btn-primary" value="${element['id']}" data-action="tourney-participants#addUser">Добавить</button>
                            </div>
                        </div>
                        <hr>`
                    container.append(div) ;
                });
            } else {
                event.target.remove();
            }
        });
    }
}