import { Controller } from "@hotwired/stimulus";
import { Modal, Alert } from "bootstrap";
import axios from "axios";

export default class extends Controller {
    static targets = [ "modal", "confirmButton", "alertContainer" ];

    static values = {
        startUrl: String,
        randomizeUrl: String,
        endUrl: String,
        removeUrl: String,
    };

    static successAlert = "";
    static errorAlert = ""

    confirmModal;
    relatedTarget;

    connect() {
        this.confirmModal = new Modal(this.modalTarget);
    }

    getParamsWithId(object) {
        let tourneyObject = object.closest('.tourney-object');
        let params = new URLSearchParams();
        params.append('id', tourneyObject.id);
        return params;
    }

    start(event) {
        let params = this.getParamsWithId(event.target);
        axios.post(this.startUrlValue, params)
            .then(function (response) {
                console.log(response);
            }, function (error) {
                console.log(error);
            });
    }

    randomize(event) {
        let params = this.getParamsWithId(event.target);
        axios.post(this.randomizeUrlValue, params)
            .then(function (response) {
                console.log(response.data);
            }, function (error) {
                console.log(error.data);
            });
    }

    end(event) {

    }

    remove(event) {
        this.relatedTarget = event.target;

        this.confirmModal.show();
    }

    confirm(event) {
        let tourneyObject = this.relatedTarget.closest('.tourney-object');

        let confirmButton = this.confirmButtonTarget;

        let oldButtonContent = confirmButton.innerHTML;

        confirmButton.innerHTML =
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
             </div>`;
        confirmButton.disabled = true;

        let tourneyId = tourneyObject.id;

        let modal = this.confirmModal;

        let data = new URLSearchParams();
        data.append('id', tourneyId);

        axios.post(this.removeUrlValue, data)
            .then(
                function (response) {
                    confirmButton.innerHTML = oldButtonContent;
                    confirmButton.disabled = false;
                    if(response.data.success) {
                        tourneyObject.remove();
                        modal.hide();
                    }
                }
        );
    }
}