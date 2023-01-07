import {Controller} from "@hotwired/stimulus";
import {Modal} from "bootstrap";
import axios from "axios";

export default class extends Controller {
    static targets = ["modal", "confirmButton"];

    static values = {
        startUrl: String,
        randomizeUrl: String,
        endUrl: String,
        removeUrl: String,
        resetUrl: String,
    };

    confirmModal;
    relatedTarget;
    alertContainer;

    connect() {
        this.confirmModal = new Modal(this.modalTarget);
        this.alertContainer = document.getElementById('alert-container');
    }

    async start(event) {
        try {
            let response = await axios.post(this.startUrlValue);
            this.alert('success', response.data.success);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    async randomize(event) {
        try {
            let response = await axios.post(this.randomizeUrlValue);
            this.alert('success', response.data.success);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    async reset(event) {
        try {
            let response = await axios.post(this.resetUrlValue);
            this.alert('success', response.data.message);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    async end(event) {
        try {
            let response = await axios.post(this.endUrlValue);
            this.alert('success', response.data.success);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    remove(event) {
        this.relatedTarget = event.target;

        this.confirmModal.show();
    }

    alert(key, text) {
        this.alertContainer.innerHTML = [
            `<div class="alert alert-${key} alert-dismissible" role="alert">`,
            `   <div>${text}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('');
    }

    async confirm(event) {
        let tourneyObject = this.relatedTarget.closest('.tourney-object');

        let confirmButton = this.confirmButtonTarget;

        let oldButtonContent = confirmButton.innerHTML;

        confirmButton.innerHTML =
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
             </div>`;
        confirmButton.disabled = true;

        let modal = this.confirmModal;

        let response = await axios.post(this.removeUrlValue);
        confirmButton.innerHTML = oldButtonContent;
        confirmButton.disabled = false;
        if (response.data.success) {
            tourneyObject.remove();
            modal.hide();
            this.disconnect();
        }
    }
}