const tokenKey = 'authToken';

let authorized = false;
let token = null;

let setAuthorized = function(value, token) {
    authorized = value;
    if (!authorized) {
        removeToken();
    } else if (authorized && token) {
        setToken(token)
    }
};

let isAuthorized = function() {
    return authorized;
};

let setToken = function(value) {
    token = value;
    try {
        localStorage.setItem(tokenKey, value);
    } catch (e) {
    }
};

let getToken = function() {
    token = getQueryToken();
    if (!token) {
        try {
            token = localStorage.getItem(tokenKey);
        } catch (e) {
        }
    }
    return token;
};

let removeToken = function() {
    token = null;
    try {
        localStorage.removeItem(tokenKey);
    } catch (e) {
    }
};

let getQueryToken = function() {
    let matches = location.search.match(/(^\?|&)token=(.*)($|&)/);
    return matches && matches.length && matches[2];
};

let addTokenToQuery = function(query) {
    if (!/(^\?|&)token=/.test(query)) {
        let tokenPrefix = query.indexOf('?') !== -1 ? '&' : '?';
        let token = getToken();
        query = `${query}${tokenPrefix}token=${token}`;
    }
    return query;
};

/**
 * Handles restricted route enter. Called with a context different from Auth object.
 * @param nextState
 * @param replace
 * @param callback
 */
// TODO Try to move auth attempt to root route onenter handler
let onRestrictedRouteEnter = function(nextState, replace, callback) {
    // See if there is a token in the url (i.e. an attempt to authorize) or in local storage.
    let attemptAuthorize = getQueryToken() || getToken();

    // If user is not authorized, but there is a token, request server auth.
    if (!isAuthorized() && attemptAuthorize) {
        $.authorizedXHR({
            url: '/backend/auth',
            dataType: 'json',
            cache: false
        })
            .always(() => {
                // If server auth is successful, proceed to the url, but without the token parameter
                if (isAuthorized()) {
                    // Do not allow authorized users on login page: redirect them to dashboard
                    let pathname = /^\/?login/.test(nextState.location.pathname) ? '/dashboard' : nextState.location.pathname;
                    replace({
                        pathname: pathname
                    });
                // Otherwise, go to auth error page with a link to login form
                } else {
                    replace({
                        pathname: '/auth-fail',
                        state: {nextPathname: nextState.location.pathname}
                    });
                }
                callback();
            });
    // If there is no token in the url (i.e. no attempt to authorize), simply redirect to login page
    } else if (!isAuthorized()) {
        replace({
            pathname: '/login',
            state: {nextPathname: nextState.location.pathname}
        });
        callback();
    // If user is authorized, proceed to requested url.
    } else {
        callback();
    }
};

module.exports = {
    setAuthorized: setAuthorized,
    isAuthorized: isAuthorized,
    getToken: getToken,
    onRestrictedRouteEnter: onRestrictedRouteEnter,
    addTokenToQuery: addTokenToQuery
};