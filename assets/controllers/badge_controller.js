import { Controller } from "@hotwired/stimulus";
import { Tooltip, Modal } from "bootstrap";
import axios from "axios";

export default class extends Controller {

    static targets = [ "modal", "confirmButton" ];

    static values = {
        removeBadgeUrl: String,
    }

    confirmModal;
    removeTarget;

    connect() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

        this.confirmModal = new Modal(this.modalTarget);
    }

    remove(event) {
        this.removeTarget = event.target;
        this.confirmModal.show();
    }

    modalConfirm(event) {
        let badgeObject = this.removeTarget.closest('.badge-object');

        let confirmButton = this.confirmButtonTarget;

        let oldButtonContent = confirmButton.innerHTML;

        confirmButton.innerHTML =
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
             </div>`;
        confirmButton.disabled = true;

        let badgeId = this.removeTarget.id;

        let modal = this.confirmModal;

        console.log(badgeId);
        axios.post(this.removeBadgeUrlValue, { id: badgeId })
            .then(
                function (response) {
                    confirmButton.innerHTML = oldButtonContent;
                    confirmButton.disabled = false;
                    if(response.data.success) {
                        badgeObject.remove();
                        modal.hide();
                    }
                }
            );
    }
}