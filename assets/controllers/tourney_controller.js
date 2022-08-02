import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";
import axios from "axios";

export default class extends Controller {
    static targets = [ "modal", "confirmButton" ];

    static values = {
        removeUrl: String,
    };

    confirmModal;
    relatedTarget;

    connect() {
        this.confirmModal = new Modal(this.modalTarget);
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

        let tourneyId = this.relatedTarget.id;

        let modal = this.confirmModal;

        let data = new URLSearchParams();
        data.append('id', tourneyId);

        console.log(tourneyId);
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