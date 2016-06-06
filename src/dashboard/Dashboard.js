import React from 'react';

import PreviousIssues from './PreviousIssues';
import './dashboard.sass';

export default class Dashboard extends React.Component {
    constructor(props) {
       super(props);

       this.state = {
           data: {
               issues: []
           },
           loaded: false
       };
   }

    componentDidMount() {
        $.authorizedXHR({
                url: '/backend/dashboard',
                dataType: 'json',
                cache: false
            })
            .then((response, textStatus, xhr) => {
                this.setState({
                    data: response.data,
                    loaded: true
                });
            })
            .fail(() => {
                console.error('Не могу получить данные');
            });
    }

    render() {
        let data = {
            issues: this.state.data.issues
        };
        let previousIssues = this.state.loaded ? (<PreviousIssues {...data}/>) : (<div className="spinner"></div>);
        return (
            <div className="dashboard">
                <h1 className="dashboard-title">Личный кабинет</h1>
                {previousIssues}
            </div>
        );
    }
}
