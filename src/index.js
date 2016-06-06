import 'bootstrap/dist/css/bootstrap.min.css';

import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { Router, Route, browserHistory, IndexRedirect} from 'react-router'

import App from './App'
import Auth from './Auth';
import Dashboard from './dashboard/Dashboard'
import LoginForm from './login/LoginForm'
import AuthFail from './login/AuthFail'

global.moment = require('moment');
moment.locale('ru');

global.$ = require('jquery');

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
    return $.ajax(options).always(function(data, textStatus, xhr) {
        // If request returns 200 response, set authorized to true
        if (xhr.status === 200) {
            Auth.setAuthorized(true, token);
        // If request returns 401 Unauthorized response, remove auth token and set authorized to false
        } else if (xhr.status === 401) {
            Auth.setAuthorized(false);
        }
    });
};

// TODO Redirection from / to dashboard does not work if user is not logged in (no token in storage)
// <IndexRedirect to="/dashboard" />
ReactDOM.render((
    <Router history={browserHistory}>
        <Route path="/" component={App}>
            <IndexRedirect to="/dashboard" />
            <Route path="login" component={LoginForm} />
            <Route path="auth-fail" component={AuthFail} />
            <Route path="dashboard" component={Dashboard} onEnter={Auth.onRestrictedRouteEnter} />
        </Route>
    </Router>
), document.getElementById('root'));