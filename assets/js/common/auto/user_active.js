import Routing from '../../routing.js';

if (global.userActiveInterval) {
    setInterval(function() {
        app.request(Routing.generate('api_user_active'));
    }, global.userActiveInterval);
}
