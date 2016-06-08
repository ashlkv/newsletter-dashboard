import 'bootstrap/dist/css/bootstrap.min.css';

import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { Router, Route, IndexRedirect, useRouterHistory} from 'react-router'
import createBrowserHistory from 'history/lib/createBrowserHistory'

import App from './App'
import Auth from './Auth';
import Dashboard from './dashboard/Dashboard'
import LoginForm from './login/LoginForm'
import AuthFail from './login/AuthFail'

global.moment = require('moment');
moment.locale('ru');

global.$ = require('jquery');

let config = require(`./config/config.${process.env.NODE_ENV}.json`);

const browserHistory = useRouterHistory(createBrowserHistory)({
    basename: config.baseUrl
});

/**
 * Adds Authorization header to an XHR request and sets Auth.authorized property to true or false depending on server response.
 * @param {Object} options
 */
$.authorizedXHR = function(options) {
    // Get authorization JWT token, if any, and send it in Authorization header like this:
    // Authorization: Bearer <token>
    let token = Auth.getToken();
    if (token) {
        options.headers = _.extend(options.headers, {
            Authorization: `Bearer ${token}`
        });
    }
    // Prepending base url to all request urls
    options.url = config.baseUrl.replace(/\/$/, '') + options.url;

    return $.ajax(options)
        .always(function(data, textStatus, xhr) {
            // If request returns 200 response, set authorized to true
            if (xhr.status === 200) {
                Auth.setAuthorized(true, token);
            // If request returns 401 Unauthorized response, remove auth token and set authorized to false
            } else if (xhr.status === 401) {
                Auth.setAuthorized(false);
            }
        })
        .then(function(response) {
            // No way to reject a current promise within then() except by creating a new promise with $.Deferred()
            // Adding an statusText to look like xhr object that is passed to fail() function
            return response.error ? $.Deferred().reject({statusText: response.error}) : response;
        })
        .fail(function(response, textStatus) {
            // Handling json parse error
            if (textStatus && textStatus === 'parsererror') {
                // Adding parse error text to statusText
                response.statusText = 'Синтаксическая ошибка';
            // Handling http errors
            } else if (response.status) {
                // Tweaking status text by adding status code
                response.statusText = `${response.status} — ${response.statusText}`;
            }
        });
};

ReactDOM.render((
    <Router history={browserHistory}>
        <Route path="/" component={App}>
            <IndexRedirect to="/dashboard" />
            <Route path="login" component={LoginForm} onEnter={Auth.onLoginFormEnter} />
            <Route path="auth-fail" component={AuthFail} />
            <Route path="dashboard" component={Dashboard} onEnter={Auth.onRestrictedRouteEnter} />
        </Route>
    </Router>
), document.getElementById('root'));
