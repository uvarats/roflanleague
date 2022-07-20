import { Controller } from "@hotwired/stimulus";
import axios from 'axios';

export default class extends Controller {

    static values = {
        submitUrl: String,
    };

    static targets = [ "form", "errors" ];

    submit(event) {
        event.preventDefault();
        event.stopPropagation();

        let target = this.formTarget;
        let form = target.querySelector('.needs-validation');

        let callbacks = [this.validateNickname, this.validateEmail, this.validatePassword, this.validateRepeat];
        let validationResult = [];
        for(let i = 0; i < 4; i++) {
            validationResult.push(this.validate(form[i], callbacks[i]));
        }
        console.log(validationResult);
        if(validationResult.includes(false)) {
            return;
        }

        let formData = new FormData(form);

        let errorsTarget = this.errorsTarget;
        let oldButtonData = event.target.innerHTML;
        event.target.disabled = true;
        event.target.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        axios.post(this.submitUrlValue, formData)
            .then(function (response) {
                if(response.data.error) {
                    event.target.disabled = false;
                    event.target.innerHTML = oldButtonData;
                    errorsTarget.innerText = response.data.error;
                }
                if(response.data.success) {
                    target.innerHTML = `<div class="alert alert-success" role="alert">
                        ${response.data.success}
                    </div>`;
                }
            });
    }

    validate(target, callback) {
        let validationResult = callback(target.value);
        if (validationResult) {
            target.classList.remove('is-invalid');
            target.classList.add('is-valid');
        } else {
            target.classList.remove('is-valid');
            target.classList.add('is-invalid');
        }
        return validationResult;
    }

    nickname(event) {
        this.validate(event.target, this.validateNickname);
    }

    email(event) {
        this.validate(event.target, this.validateEmail);
    }

    password(event) {
        this.validate(event.target, this.validatePassword);
    }

    repeat(event) {
        this.validate(event.target, this.validateRepeat)
    }

    validateNickname(value) {
        return !!value;
    }

    validateEmail(value) {
        return String(value)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            ) ?? false;
    }

    validatePassword(value) {
        return value.length >= 6;
    }

    validateRepeat(value) {
        let password = document.getElementById('registration_form_plainPassword_first').value;
        return password === value && password !== "";
    }
}