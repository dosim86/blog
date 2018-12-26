import Routing from './routing.js';

if (global.userActiveInterval) {
    setInterval(function() {
        appGet(Routing.generate('api_user_active'), { token: global.userActivityToken });
    }, global.userActiveInterval);
}
