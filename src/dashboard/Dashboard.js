import React from 'react';

import PreviousIssues from './PreviousIssues';
import './dashboard.sass';

export default class Dashboard extends React.Component {
    constructor(props) {
       super(props);

       this.state = {
           data: {
               issues: []
           }
       };
   }

    componentDidMount() {
        $.authorizedXHR({
                url: '/backend/dashboard',
                dataType: 'json',
                cache: false
            })
            .then((response, textStatus, xhr) => {
                this.setState({data: response.data});
            })
            .fail(() => {
                console.error('Не могу получить данные');
            });
    }

    render() {
        let data = {issues: this.state.data.issues};
        return (
            <div className="dashboard">
                <h1>Личный кабинет</h1>
                <PreviousIssues {...data}/>
            </div>
        );
    }
}
