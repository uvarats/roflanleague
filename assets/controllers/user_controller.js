import { Controller } from "@hotwired/stimulus";
import axios from "axios";
import {Tooltip} from "bootstrap";

export default class extends Controller {
    static values = {
        switchUrl: String,
        verifyUrl: String,
        addBadgeUrl: String,
        removeBadgeUrl: String,
    };

    static targets = [ 'badgeId' ];

    // для добавления тултипов новых бейджей
    tooltipList;

    banned = `Блокировка: <span class="text-danger">забанен</span>`;
    notBanned = `Блокировка: <span class="text-success">нет</span>`;

    verified = `<p>Статус почты: <span class="text-success">подтверждена</span></p>`;
    notVerified = `<p>Статус почты: <span class="text-danger">не подтверждена</span></p>`;

    connect() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        this.tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
    }

    switchUserStatus(event) {
        let banStatus = document.getElementById('ban-status');
        let banned = this.banned;
        let notBanned = this.notBanned;


        axios.post(this.switchUrlValue)
            .then(function (response) {
                if (response.data.error) {
                    return;
                }

                if (response.data.newStatus) {
                    event.target.innerHTML = "Разблокировать";
                    event.target.classList.replace('btn-outline-warning', 'btn-outline-success');
                    banStatus.innerHTML = banned;
                }

                if(!response.data.newStatus) {
                    event.target.innerHTML = "Заблокировать";
                    event.target.classList.replace('btn-outline-success', 'btn-outline-warning');
                    banStatus.innerHTML = notBanned;
                }
            });
    }

    verify(event) {
        let verifyStatus = document.getElementById('verify-status');
        let verified = this.verified;
        let notVerified = this.notVerified;

        axios.post(this.verifyUrlValue)
            .then(function (response) {
                if (response.data.success) {
                    event.target.remove();
                    verifyStatus.innerHTML = verified;
                }
            });
    }

    addBadge(event) {
        let params = new URLSearchParams();
        params.append('id', this.badgeIdTarget.value);
        let tooltipList = this.tooltipList;


        axios.post(this.addBadgeUrlValue, params)
            .then(function (response) {
                if (response.data.badge) {
                    let badgeData = response.data.badge;

                    let badges = document.getElementById('badge-container');
                    let badgeItem = document.createElement('div');
                    badgeItem.classList.add('badge-item', 'd-flex', 'align-items-center');
                    badgeItem.id = badgeData['id'];

                    badgeItem.innerHTML =
                        `<span class="badge rounded-pill ms-2"
                                  style="background-color: ${badgeData['hexCode']}"
                                  data-bs-toggle="tooltip"
                                  data-bs-placement="bottom"
                                  data-bs-title="${badgeData['text']}"
                            >
                                ${badgeData['name']}
                            </span>
                        <button type="button" data-action="user#removeBadge" class="btn-close" aria-label="Remove"></button>`;
                    badges.append(badgeItem);
                    tooltipList.push(new Tooltip(badgeItem.querySelector('.badge')));
                }
            });
    }

    removeBadge(event) {
        let badgeItem = event.target.closest('.badge-item')

        let params = new URLSearchParams();
        params.append('id', badgeItem.id);

        axios.post(this.removeBadgeUrlValue, params)
            .then(function (response) {
                if (response.data.success) {
                    badgeItem.remove();
                }
            });
    }
}
