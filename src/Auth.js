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
 * Determines if we should try and authorize the user with backend auth request
 * @returns {boolean}
 */
let authValidationRequired = function() {
    // See if there is a token in the url (i.e. an attempt to authorize) or in local storage.
    return !isAuthorized() && (getQueryToken() || getToken());
};

/**
 * Makes auth request
 * @returns {Promise}
 */
let validateAuth = function() {
    return $.authorizedXHR({
            url: '/backend/auth',
            dataType: 'json',
            cache: false
        });
};

/**
 * Handles login form enter: redirects authorized users to dashboard
 * @param nextState
 * @param replace
 * @param callback
 */
let onLoginFormEnter = function(nextState, replace, callback) {
    // If user is already authorized, redirect to dashboard
    if (isAuthorized()) {
        replace({
            pathname: '/dashboard'
        });
        callback();
    // If it is unclear if user is authorized, make an auth validation request, then decide.
    } else if (authValidationRequired()) {
        validateAuth().then(function() {
            replace({
                pathname: '/dashboard'
            });
            callback();
        }).fail(function() {
            // If auth validation failed, proceed to login form
            callback();
        });
    // If user is not authorized, proceed to login form
    } else {
        callback();
    }
};

/**
 * Handles restricted route enter. Called with a context different from Auth object.
 * @param nextState
 * @param replace
 * @param callback
 */
let onRestrictedRouteEnter = function(nextState, replace, callback) {
    // If it is unclear if user is authorized, request server auth validation.
    if (authValidationRequired()) {
        validateAuth().then(function() {
            // If server auth is successful, proceed to the url, but without the token parameter
            replace({
                pathname: nextState.location.pathname
            });
            callback();
        // If auth validation failed, go to auth error page with a link to login form
        }).fail(function() {
            replace({
                pathname: '/auth-fail',
                state: {nextPathname: nextState.location.pathname}
            });
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
    onLoginFormEnter: onLoginFormEnter,
    addTokenToQuery: addTokenToQuery
};