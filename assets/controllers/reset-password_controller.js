import { Controller } from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    static values = {
        changeSubmitUrl: String,
        successUrl: String,
    };
    static targets = [ "requestForm", "changeForm", "resetErrors" ];

    submit(event) {
        const form = this.requestFormTarget.querySelector('.needs-validation');
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    }

    changeFormSubmit(event) {
        event.preventDefault();
        const form = this.changeFormTarget.querySelector('.needs-validation');
        let formData = new FormData(form);
        const alert = this.resetErrorsTarget;
        const successUrl = this.successUrlValue;

        let oldButtonData = event.target.innerHTML;

        event.target.disabled = true;
        event.target.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        axios.post(this.changeSubmitUrlValue, formData)
            .then(function (response) {
                if(response.data.error) {
                    event.target.innerHTML = oldButtonData;
                    event.target.disabled = false;
                    alert.innerText = response.data.error;
                }
                if(response.data.success) {
                    location.replace(successUrl);
                }
            });
    }

}