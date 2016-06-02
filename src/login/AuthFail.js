import React from 'react';

import './auth-fail.sass';
import { Link } from 'react-router'

export default class AuthFail extends React.Component {
    render() {
        return (
            <div className="auth-fail">
                {/* TODO Approve this text */}
                Не удалось авторизоваться. Попробуйте получить <Link to="/login">ссылку для входа</Link> ещё раз.
            </div>
        )
    }
}
