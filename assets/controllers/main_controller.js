import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        loginUrl: String,
        registerUrl: String,
    }

    login() {
        location.replace(this.loginUrlValue)
    }

    register() {
        location.replace(this.registerUrlValue);
    }
}