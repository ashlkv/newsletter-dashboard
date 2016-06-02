let _ = require('lodash');

import React from 'react';

import './login.sass';

export default class LoginForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            email: null,
            emailSent: false
        };
    }

    onEmailChange(event) {
        this.setState({email: event.target.value});
    }

    onSubmit(e) {
        e.preventDefault();
        let email = _.trim(this.state.email);

        $.authorizedXHR({
            url: '/backend/token/' + email,
            dataType: 'json',
            cache: false
        });

        // TODO Set emailSent back to false on route enter
        this.setState({
            emailSent: true
        });
    }

    render() {
        let content;
        if (this.state.emailSent) {
            content = <p className="login-form-message">Ссылка для входа отправлена на почту.</p>;
        } else {
            content = (
                <form className="form-inline login-form" onSubmit={this.onSubmit.bind(this)}>
                    <label className="login-form-label" htmlFor="login-form-email">Email, который подписан на рассылку:</label>
                    <div className="form-group login-form-email-group">
                        <input type="email" className="form-control login-form-email-input" placeholder="Email" id="login-form-email" value={this.state.email} onChange={this.onEmailChange.bind(this)} />
                        <button type="submit" className="btn btn-primary login-form-submit">Войти</button>
                    </div>
                    <p className="login-from-note">На почту придёт ссылка для входа в личный кабинет.</p>
                </form>
            )
        }

        return (
            <div className="login-form-box">
                {content}
            </div>
        );
    }
}
