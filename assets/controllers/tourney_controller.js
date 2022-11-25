import {Controller} from "@hotwired/stimulus";
import {Modal} from "bootstrap";
import axios from "axios";

export default class extends Controller {
    static targets = ["modal", "confirmButton", "alertContainer"];

    static values = {
        startUrl: String,
        randomizeUrl: String,
        endUrl: String,
        removeUrl: String,
    };

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

    async start(event) {
        let params = this.getParamsWithId(event.target);
        try {
            let response = await axios.post(this.startUrlValue, params);
            this.alert('success', response.data.success);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    async randomize(event) {
        let params = this.getParamsWithId(event.target);
        try {
            let response = await axios.post(this.randomizeUrlValue, params);
            this.alert('success', response.data.success);
        } catch (e) {
            this.alert('danger', e.response.data.detail);
        }
    }

    async end(event) {
        let params = this.getParamsWithId(event.target);
        try {
            let response = await axios.post(this.endUrlValue, params);
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
        this.alertContainerTarget.innerHTML = [
            `<div class="alert alert-${key} alert-dismissible" role="alert">`,
            `   <div>${text}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('');
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
                    if (response.data.success) {
                        tourneyObject.remove();
                        modal.hide();
                    }
                }
            );
    }
}