import { Controller } from "@hotwired/stimulus";
import axios from 'axios';

export default class extends Controller {
    static targets = [ "form" ];

    submit(event) {
        event.preventDefault();
        event.stopPropagation();
        let form = this.formTarget.querySelector('.needs-validation');
        let formData = new FormData(form);

        axios.post('/signup', formData).then(function (response) {
            console.log(response.data);
        });
    }

    validate(target, callback) {
        let validationResult = callback(target.value);
        if (validationResult) {
            event.target.classList.remove('is-invalid');
            event.target.classList.add('is-valid');
        } else {
            event.target.classList.remove('is-valid');
            event.target.classList.add('is-invalid');
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
            );
    }

    validatePassword(value) {
        return value.length >= 6;
    }

    validateRepeat(value) {
        let password = document.getElementById('registration_form_plainPassword_first').value;
        console.log(password);
        return password === value && password !== "";
    }
}