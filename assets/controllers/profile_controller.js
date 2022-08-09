import { Controller } from "@hotwired/stimulus";
import {Tooltip} from "bootstrap";

export default class extends Controller {

    tooltipList;

    connect() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        this.tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
    }
}