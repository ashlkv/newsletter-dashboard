let _ = require('lodash');

import React from 'react';

import './login.sass';

const successStatus = 'success';
const errorStatus = 'error';

export default class LoginForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            email: null,
            status: null,
            message: null
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
        }).then(() => {
            this.setState({
                status: successStatus
            });
        }).fail((xhr) => {
            this.setState({
                status: errorStatus,
                message: `${xhr.status} — ${xhr.statusText}`
            });
        });
    }

    render() {
        let content;
        if (this.state.status === errorStatus) {
            content = (
                <p className="login-form-message login-form-error">
                    Ошибка при отправке письма:
                    <br/>
                    {this.state.message}
                </p>);
        } else if (this.state.status === successStatus) {
            content = <p className="login-form-message">Ссылка для входа отправлена на почту.</p>;
        } else {
            content = (
                <form className="form-inline login-form" onSubmit={this.onSubmit.bind(this)}>
                    <label className="login-form-label" htmlFor="login-form-email">Email, который подписан на рассылку:</label>
                    <div className="form-group login-form-email-group">
                        <input type="email" className="form-control login-form-email-input" placeholder="Email" id="login-form-email" value={this.state.email} onChange={this.onEmailChange.bind(this)} />
                        <button type="submit" disabled={!this.state.email} className="btn btn-primary login-form-submit">Войти</button>
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
