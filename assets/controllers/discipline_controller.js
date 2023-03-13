import { Controller } from '@hotwired/stimulus';
import axios from "axios";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        deleteUrl: String,
        deleteCsrf: String,
    }

    async delete(event) {
        if (!confirm('Вы уверены?')) {
            return;
        }

        let params = new URLSearchParams();
        params.append('_token', this.deleteCsrfValue);
        let response = await axios.post(this.deleteUrlValue, params);

        window.location.replace(response.request.responseURL);
    }
}
