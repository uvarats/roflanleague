import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['matchObject'];
    static values = {
        homePlayerId: Number,
        awayPlayerId: Number,
        homePlayerOdds: Number,
        awayPlayerOdds: Number,
        tourneyId: Number,
        challongeMatchId: Number,
        resultRoute: String,
        randomRoute: String,
    };
    firstWin(event) {
        this.sendResult('w1', this.resultRouteValue)
    }

    secondWin(event) {
        this.sendResult('w2', this.resultRouteValue);
    }

    randomResult(event) {
        this.sendResult(null, this.randomRouteValue); // null because needed random result
    }

    async sendResult(result, route) {

        let data = {
            'result': result,
            'matchId': this.challongeMatchIdValue,
            'tourneyId': this.tourneyIdValue,
            'homePlayerId': this.homePlayerIdValue,
            'awayPlayerId': this.awayPlayerIdValue,
            'homePlayerOdds': this.homePlayerOddsValue,
            'awayPlayerOdds': this.awayPlayerOddsValue
        };

        let params = new URLSearchParams();
        params.append('result', JSON.stringify(data))


        try {
            let response = await axios.post(route, params);
            if (response.data.result) {
                this.matchObjectTarget.remove();
                this.disconnect();
            }
        } catch (e) {
            console.log(e.response.data);
        }
    }
}
