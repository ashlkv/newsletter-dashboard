let _ = require('lodash');

import React from 'react';

import './login.sass';

const status = {
    success: 'success',
    error: 'error'
};

export default class LoginForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            email: null,
            status: null,
            progress: false,
            message: null
        };
    }

    onEmailChange(event) {
        this.setState({email: event.target.value});
    }

    onSubmit(e) {
        e.preventDefault();
        this.refs.email.blur();
        let email = _.trim(this.state.email);

        this.setState({
            progress: true
        });

        $.authorizedXHR({
            url: '/backend/token/' + email,
            dataType: 'json',
            cache: false
        }).then(() => {
            this.setState({
                status: status.success
            });
        }).fail((xhr) => {
            this.setState({
                status: status.error,
                message: xhr.statusText
            });
        }).done(() => {
            this.setState({
                progress: false
            });
        });
    }

    render() {
        let content;

        if (this.state.status === status.error) {
            let rawMessage = {__html: this.state.message};
            content = (
                <div className="login-form-message login-form-error">
                    Ошибка при отправке письма:
                    <div dangerouslySetInnerHTML={rawMessage}></div>
                </div>);
        } else if (this.state.status === status.success) {
            content = <div className="login-form-message" >Ссылка для входа отправлена на почту.</div>;
        } else {
            let backdrop = (<div className="modal-backdrop fade in">
                    <div className="spinner"></div>
                </div>);
            content = (
                <form className="form-inline login-form" onSubmit={this.onSubmit.bind(this)}>
                    {this.state.progress ? backdrop : null}
                    <label className="login-form-label" htmlFor="login-form-email">Email, который подписан на рассылку:</label>
                    <div className="form-group login-form-email-group">
                        <input type="email" className="form-control login-form-email-input" placeholder="Email" id="login-form-email" ref="email" value={this.state.email} onChange={this.onEmailChange.bind(this)} />
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
